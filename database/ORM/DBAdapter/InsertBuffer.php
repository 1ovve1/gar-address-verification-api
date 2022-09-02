<?php declare(strict_types=1);

namespace DB\ORM\DBAdapter;

use DB\Exceptions\IncorrectBufferInputException;
use DB\Exceptions\InvalidForceInsertConfigurationException;
use DB\ORM\DBFacade;
use SplFixedArray;

/**
 * Lazy insert abstract class
 *
 * @phpstan-import-type DatabaseContract from DBAdapter
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
    /** @var SplFixedArray - buffer of stage values */
    private SplFixedArray $buffer;

    /**
     * @param string $tableName - name of table
     * @param String[] $tableFields - table fields
     * @param int $groupInsertCount - number of groups in group insert
     * @throws InvalidForceInsertConfigurationException
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
     * @param array<mixed> $tableFields - fields to create
     * @param int $groupInsertCount - stage count
     * @return void
     * @throws InvalidForceInsertConfigurationException
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
	 * @param DatabaseContract[] $insertValues - values that need add in $buffer
	 * @return void
	 * @throws IncorrectBufferInputException
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
}
