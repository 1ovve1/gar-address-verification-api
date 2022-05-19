<?php declare(strict_types=1);

namespace GAR\Database;

use GAR\Database\DBAdapter\DBAdapter;
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
  public function __construct(DBAdapter $db)
  {
    parent::__construct(
      $db,
      DBFacade::genTableNameByClassName(get_class($this)),
      intval($_SERVER['DB_BUFF']),
      $this->fieldsToCreate()
    );
    Log::write(
      Msg::LOG_DB_INIT->value,
      $this->getTableName(),
      Msg::LOG_COMPLETE->value
    );
  }

  protected function fieldsToCreate() : ?array
  {
    return null;
  }
}
