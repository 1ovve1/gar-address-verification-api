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
    /** @var int $bufferSize - size of buffer */
    private readonly int $bufferSize;
    /** @var int $bufferCursor - cursor of the buffer index */
    private int $bufferCursor = 0;
    /** @var array<string, array<DatabaseContract>> - buffer of stage values */
    protected array $buffer;

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

	    $this->bufferCursor = 0;
	    $this->bufferSize = $groupInsertCount;

		foreach ($tableFields as $columnName) {
			$this->buffer[$columnName] = [];
		}
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

	public function genVarsFromCurrentBufferCursor(): string
	{
		return DBFacade::genInsertVars(
			$this->getTableFieldsCount(), $this->getCurrentBufferCursor()
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

	protected function bufferReshape(): void
	{
		$newBuffer = [];
		$oldBuffer = $this->getBuffer();
		$currCursorCount = $this->getCurrentBufferCursor();

		for ($iter = 0; $iter < $currCursorCount; ++$iter) {
			foreach ($oldBuffer as $column => $columnValues) {
				$newBuffer[$column][$iter] = $columnValues[$iter];
			}
		}

		$this->buffer = $newBuffer;
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
     * Return current buffer cursor value
     * @return int
     */
    public function getCurrentBufferCursor(): int
    {
        return $this->bufferCursor;
    }

    /**
     * Return buffer in array 1d
     * @return array<string, array<DatabaseContract>>
     */
    public function getBuffer(): array
    {
		return $this->buffer;
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

		$currCursor = $this->getCurrentBufferCursor();
		foreach ($this->getTableFields() as $index => $field) {
			$this->buffer[$field][$currCursor] = $insertValues[$index];
		}

		$this->incBufferCursor();
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
	 * @return bool - return record number (from 1) or false if value was not found
	 */
	public function checkValueInBufferExist(int|float|bool|string|null $value, string $fieldName): bool
	{
		if (!in_array($fieldName, $this->getTableFields())) {
			throw new \RuntimeException("Unknown field '{$fieldName}' for table name {$this->tableName}");
		}

		return in_array($value, $this->buffer[$fieldName], true);
	}

	/**
	 * @param array<DatabaseContract> $record
	 * @return bool - return pos of record (from 1) of false if record was not found
	 */
	function checkIfRecordInBufferExist(array $record): bool
	{
		$columns = $this->getTableFields();
		$currentBufferCursor = $this->getCurrentBufferCursor();

		$decision = true;
		for ($row = 0; $row < $currentBufferCursor; ++$row) {

			foreach($columns as $index => $column) {
				$decision = $this->buffer[$column][$row] === $record[$index];
				if (false === $decision) {
					break;
				}
			}

			if (true === $decision) {
				break;
			}
		}

		return $decision;
	}
}
