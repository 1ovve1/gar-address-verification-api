<?php declare(strict_types=1);

namespace GAR\Database;

use GAR\Database\DBAdapter\DBAdapter;
use GAR\Database\Table\MetaTable;
use GAR\Database\Table\SQLBuilder;
use GAR\Logger\Log;
use GAR\Logger\Msg;

/**
 * CONCRETE TABLE CLASS
 *
 * IMPLEMENTS ABSTRACTNESS METHODS
 * (OR MODIFIED THEM)
 */
abstract class ConcreteTable extends SQLBuilder
{
  public function __construct(DBAdapter $db, bool $createMetaTable = true)
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
      ($createMetaTable) ? $metaTable: null,
      intval($_SERVER['DB_BUFF'])
    );
    Log::write(
      Msg::LOG_DB_INIT->value,
      $this->metaTable?->getTableName() ?? '',
      Msg::LOG_COMPLETE->value
    );
  }

  public static function getInstance(DBAdapter $db) : static
  {
    static $instance = null;
    if (is_null($instance)) {
      $instance = new static($db);
    }

    return $instance;
  }

  protected function fieldsToCreate() : ?array
  {
    return null;
  }
}
