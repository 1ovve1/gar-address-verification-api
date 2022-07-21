<?php

declare(strict_types=1);

namespace GAR\Database;

use GAR\Database\DBAdapter\DBAdapter;
use GAR\Database\Table\MetaTable;
use GAR\Database\Table\SQL\QueryModel;
use GAR\Database\Table\SQLBuilder;
use GAR\Logger\Log;
use GAR\Logger\Msg;

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
        Log::write(
            Msg::LOG_DB_INIT->value,
            $this->metaTable?->getTableName() ?? '',
            Msg::LOG_COMPLETE->value
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
