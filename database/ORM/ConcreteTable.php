<?php

declare(strict_types=1);

namespace DB\ORM;

use DB\ORM\DBAdapter\DBAdapter;
use DB\ORM\Table\MetaTable;
use DB\ORM\Table\SQL\QueryModel;
use DB\ORM\Table\SQLBuilder;

/**
 * Concrete table classs
 */
abstract class ConcreteTable extends SQLBuilder
{
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
        parent::__construct(
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
        static $instance = null;
        if (null === $instance) {
            $instance = new static($db, $createMetaTable);
        }

        return $instance;
    }

    /**
     * return fields thath need to create in new table model
     *
     * @return ?array<string, string>
     */
    protected function fieldsToCreate(): ?array
    {
        return null;
    }
}
