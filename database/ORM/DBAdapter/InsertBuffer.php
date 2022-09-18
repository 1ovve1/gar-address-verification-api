<?php declare(strict_types=1);

namespace DB\ORM\DBAdapter;

use DB\Exceptions\Unchecked\IncorrectBufferInputException;
use DB\Exceptions\Unchecked\InvalidForceInsertConfigurationException;
use DB\ORM\DBFacade;
use SplFixedArray;

/**
 * Lazy insert abstract class
 */
abstract class InsertBuffer
{
    /** @var string - name of table */
    private readonly string $tableName;
    /** @var String[] $tableFields - template fields */
    private readonly array $tableFields;
    /** @var int - size of buffer */
    private readonly int $bufferSize;
    /** @var int - cursor of the buffer index */
    private int $bufferCursor = 0;
    /** @var SplFixedArray<DatabaseContract> - buffer of stage values */
    private SplFixedArray $buffer;

    /**
     * @param string $tableName - name of table
     * @param String[] $tableFields - table fields
     * @param int $groupInsertCount - number of groups in group insert
     */
    public function __construct(string $tableName, array $tableFields, int $groupInsertCount)
    {
        $this->isValid($tableName, $tableFields, $groupInsertCount);

        $this->tableName = $tableName;
        $this->tableFields = $tableFields;

        $bufferSize = $groupInsertCount * count($tableFields);
        $this->bufferSize = $bufferSize;
        $this->buffer = new SplFixedArray($bufferSize);
    }


    /**
     * Create exception if input is incorrect
     *
     * @param string $tableName - name of table
     * @param array<string> $tableFields - fields to create
     * @param int $groupInsertCount - stage count
     * @return void
     */
    public static function isValid(string $tableName, array $tableFields, int $groupInsertCount): void
    {
        if ($groupInsertCount < 1) {
            throw new InvalidForceInsertConfigurationException(
                'PDOTemplate error: stages buffer needs to be more than 0'
            );
        } elseif (empty($tableFields)) {
            throw new InvalidForceInsertConfigurationException(
                'PDOTemplate error: stages buffer needs to be more than 0'
            );
        } elseif (empty($tableName)) {
            throw new InvalidForceInsertConfigurationException(
                'PDOTemplate error: stages buffer needs to be more than 0'
            );
        }
    }

	public function genVarsFromCurrentGroupNumber(): string
	{
		return DBFacade::genInsertVars(
			$this->getTableFieldsCount(), $this->getCurrentNumberOfGroups()
		);
	}

    /**
     * Return table name
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Return size of value buffer
     * @return int
     */
    public function getBufferSize(): int
    {
        return $this->bufferSize;
    }

    /**
     * Increment buffer cursor
     * @return void
     */
    public function incBufferCursor(): void
    {
        $this->bufferCursor += 1;
    }

    /**
     * Return true if buffer is full
     * @return bool
     */
    public function isBufferFull(): bool
    {
        return $this->bufferCursor === $this->bufferSize;
    }

    /**
     * Return true if buffer is not empty
     * @return bool
     */
    public function isBufferNotEmpty(): bool
    {
        return $this->bufferCursor !== 0;
    }

    /**
     * Return table fields in current template
     * @return String[]
     */
    public function getTableFields(): array
    {
        return $this->tableFields;
    }

    /**
     * Return table fields count
     * @return int
     */
    public function getTableFieldsCount(): int
    {
        return count($this->tableFields);
    }

    /**
     * Return current number of groups
     * @return int
     */
    public function getCurrentNumberOfGroups(): int
    {
        return $this->bufferCursor / count($this->tableFields);
    }

    /**
     * Return local SplFixedBuffer buffer in array
     * @return DatabaseContract[]
     */
    public function getBuffer(): array
    {
        return $this->buffer->toArray();
    }

    /**
     * Slice local buffer with current cursor value
     * @return DatabaseContract[]
     */
    public function getBufferSlice(): array
    {
        $array = new SplFixedArray($this->bufferCursor);
        for ($i = 0; $i < $this->bufferCursor; ++$i) {
            $array[$i] = $this->buffer[$i];
        }

        return $array->toArray();
    }

	/**
	 * Set stage buffer by $insertValues
	 * @param array<DatabaseContract> $insertValues - values that need add in $buffer
	 * @return void
	 */
    public function setBuffer(array $insertValues): void
    {
        if (count($insertValues) !== $this->getTableFieldsCount()) {
            throw new IncorrectBufferInputException($this->getTableFieldsCount(), $insertValues);
        }

        foreach ($insertValues as $value) {
            $this->buffer[$this->bufferCursor] = $value;
            $this->incBufferCursor();
        }
    }

    /**
     * Reset cursor value
     * @return void
     */
    public function resetBufferCursor(): void
    {
        $this->bufferCursor = 0;
    }

	/**
	 * check if value exists in current buffer
	 * @param DatabaseContract $value
	 * @param string $fieldName
	 * @return int|false - return record number (from 1) or false if value was not found
	 */
	public function checkValueInBufferExist(int|float|bool|string|null $value, string $fieldName): int|false
	{
		/** @var int $fieldPos */
		$fieldPos = array_flip($this->tableFields)[$fieldName] ??
			throw new \RuntimeException("Unknown field '{$fieldName}' for table name {$this->tableName}");
		$fieldsCount = $this->getTableFieldsCount();

		for ($iter = $fieldPos; $iter < $this->bufferCursor; $iter += $fieldsCount) {
			if ($this->buffer[$iter] === $value) {
				return ($iter - $fieldPos) + 1;
			}
		}

		return false;
	}

	/**
	 * @param array<DatabaseContract> $record
	 * @return int|false - return pos of record (from 1) of false if record was not found
	 */
	function checkIfRecordInBufferExist(array $record): int|false
	{
		$fieldsCount = $this->getTableFieldsCount();

		for ($iter = 0; $iter < $this->bufferCursor; $iter += $fieldsCount) {
			foreach ($record as $offset => $value) {
				if ($this->buffer[$iter + $offset] === $value) {
					return $iter + 1;
				}
			}
		}

		return false;
	}
}
