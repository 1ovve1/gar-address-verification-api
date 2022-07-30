<?php

declare(strict_types=1);

namespace DB\ORM;

use DB\ORM\DBAdapter\DBAdapter;
use DB\ORM\Table\MetaTable;
use DB\ORM\Table\SQL\DeleteQuery;
use DB\ORM\Table\SQL\EndQuery;
use DB\ORM\Table\SQL\SelectQuery;
use DB\ORM\Table\SQL\UpdateQuery;
use DB\ORM\Table\SQLBuilder;

/**
 * Concrete table classs
 */
abstract class ConcreteTable
{
	public SQLBuilder $sqlBuilder;

    /**
     * @param DBAdapter $db - database object
     * @param bool $createMetaTable - create table model option
     */
    final public function __construct(DBAdapter $db, bool $createMetaTable = true)
    {
        if ($createMetaTable) {
            $metaTable = new MetaTable(
                $db,
                DBFacade::genTableNameByClassName(get_class($this)),
                $this->fieldsToCreate()
            );
        }
        $this->sqlBuilder = new SQLBuilder(
            $db,
            ($createMetaTable) ? $metaTable : null,
            intval($_ENV['DB_BUFF'])
        );
    }

    /**
     * Return singleton instance of static object
     *
     * @param bool $createMetaTable - create table model option
     * @return ConcreteTable
     */
    public static function getInstance(bool $createMetaTable = true): ConcreteTable
    {
        static $instances = [];
		$staticClass = static::class;

        if (null === ($instances[$staticClass])) {
			$db = DBFacade::getInstance();
            $instances[$staticClass] = new static($db, $createMetaTable);
        }

        return $instances[$staticClass];
    }

    /**
     * return fields that need to create in new table model
     *
     * @return ?array<string, string>
     */
    protected function fieldsToCreate(): ?array
    {
        return null;
    }

	public static function insert(array $values, ?string $tableName = null): EndQuery
	{
		$instance = static::getInstance();

		return $instance->sqlBuilder->insert($values, $tableName);
	}

	public static function forceInsert(array $values): EndQuery
	{
		$instance = static::getInstance();

		try {
			return $instance->sqlBuilder->forceInsert($values);
		} catch (\Exception $e) {
			echo 'Error while uploading data via forceInsert' . PHP_EOL;
			var_dump($values);
			throw new RuntimeException();
		}
	}

	public static function update(string $field,
	                              mixed $value,
	                              ?string $tableName = null): UpdateQuery
	{
		$instance = static::getInstance();

		return $instance->sqlBuilder->update($field, $value, $tableName);
	}

	public static function delete(?string $tableName = null): DeleteQuery
	{
		$instance = static::getInstance();

		return $instance->sqlBuilder->delete($tableName);
	}

	public static function select(array $fields, ?array $anotherTables = null): SelectQuery
	{
		$instance = static::getInstance();

		return $instance->sqlBuilder->select($fields, $anotherTables);
	}

	public static function findFirst(string $field, mixed $value, ?string $anotherTable = null): array
	{
		$instance = static::getInstance();

		return $instance->sqlBuilder->findFirst($field, $value, $anotherTable);
	}

	public static function nameExist(string $checkName): bool
	{
		$instance = static::getInstance();

		return $instance->sqlBuilder->nameExist($checkName);
	}

	public static function execute(array $values, ?string $templateName = null): array
	{
		$instance = static::getInstance();

		return $instance->sqlBuilder->execute($values, $templateName);
	}

	public static function save(): array
	{
		$instance = static::getInstance();

		return $instance->sqlBuilder->save();
	}
}
