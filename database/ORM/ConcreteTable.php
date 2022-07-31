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

        if (!isset($instances[$staticClass])) {
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


}
