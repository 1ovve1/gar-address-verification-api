<?php

declare(strict_types=1);

namespace DB\ORM;

use DB\ORM\DBAdapter\DBAdapter;
use DB\ORM\Table\MetaTable;
use DB\ORM\Table\SQL\DeleteQuery;
use DB\ORM\Table\SQL\EndQuery;
use DB\ORM\Table\SQL\QueryModel;
use DB\ORM\Table\SQL\SelectQuery;
use DB\ORM\Table\SQL\UpdateQuery;
use DB\ORM\Table\SQLBuilder;

/**
 * Concrete table classs
 */
abstract class ConcreteTable implements QueryModel
{
	private SQLBuilder $builder;

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
        $this->builder = new SQLBuilder(
            $db,
            ($createMetaTable) ? $metaTable : null,
            intval($_SERVER['DB_BUFF'])
        );
    }

    /**
     * Return singleton instance of static object
     *
     * @param DBAdapter $db - database connection
     * @param bool $createMetaTable - create table model option
     * @return QueryModel
     */
    public static function getInstance(DBAdapter $db, bool $createMetaTable = true): QueryModel
    {
        static $instances = [];
		$staticClass = static::class;

        if (!isset($instances[$staticClass])) {
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

	/**
	 * {@inheritDoc}
	 */
	public static function insert(array $values, ?string $tableName = null): EndQuery;

	/**
	 * {@inheritDoc}
	 */
	public static function forceInsert(array $values): EndQuery;

	/**
	 * {@inheritDoc}
	 */
	public static function update(string $field, mixed $value, ?string $tableName = null): UpdateQuery;

	/**
	 * {@inheritDoc}
	 */
	public static function delete(?string $tableName = null): DeleteQuery;

	/**
	 * {@inheritDoc}
	 */
	public static function select(array $fields, ?array $anotherTables = null): SelectQuery;

	/**
	 * {@inheritDoc}
	 */
	public static function findFirst(string $field, mixed $value, ?string $anotherTable = null): array;

	/**
	 * {@inheritDoc}
	 */
	public static function nameExist(string $checkName): bool;

	/**
	 * {@inheritDoc}
	 */
	public static function execute(array $values, ?string $templateName = null): array;

	/**
	 * {@inheritDoc}
	 */
	public static function save(): array
	{

	}
}
