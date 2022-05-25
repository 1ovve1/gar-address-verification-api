<?php declare(strict_types=1);

namespace GAR\Repository;

use GAR\Database\Table\SQL\QueryModel;

class BaseRepo
{
  /**
   * @param QueryModel $database
   */
  public function __construct(
    private readonly QueryModel $database
  )
  {}

  /**
   * Return database
   *
   * @return QueryModel
   */
  public function getDatabase() : QueryModel {
    return $this->database;
  }
}