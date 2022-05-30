<?php declare(strict_types=1);

namespace GAR\Repository;

use GAR\Database\Table\SQL\QueryModel;

/**
 * Basic repository class, contains prodaction database
 */
class BaseRepo
{
  /**
   * @param QueryModel $database - prodaction databse
   */
  public function __construct(
    private readonly QueryModel $database
  )
  {}

  /**
   * Return prodaction database accsessor $database
   *
   * @return QueryModel
   */
  public function getDatabase() : QueryModel {
    return $this->database;
  }
}