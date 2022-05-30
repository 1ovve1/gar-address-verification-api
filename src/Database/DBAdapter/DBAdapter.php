<?php

namespace GAR\Database\DBAdapter;

use GAR\Database\Table\Container\Query;


/**
 * Common interface for databse connection
 *
 * @phpstan-type DatabaseContract int|float|string|bool|null
 */
interface DBAdapter
{
  public const PDO_F_ALL = \PDO::FETCH_ASSOC;
  public const PDO_F_COL = \PDO::FETCH_COLUMN;

  /**
   * Execute custom query container
   * 
   * @param  Query $query - query container
   * @return self - self
   */
  function rawQuery(Query $query) : self;
  
  /**
   * Fecthing last query by special flag
   * 
   * @param  int $flag - fetching flag
   * @return array<mixed>
   */
  function fetchAll(int $flag) : mixed;

  /**
   * Preapre query by template. Use execute for execute statement or getTemplate to get QueryTemplate onbect
   * 
   * @param  string $template - template
   * @return self - self
   */
  function prepare(string $template) : self;

  /**
   * Execute prepared statement. Then use fetchAll to get result
   * 
   * @param  array<DatabaseContract> $values - values that need to execute
   * @return self
   */
  function execute(array $values) : self;

  /**
   * @return QueryTemplate - last prepared template
   */
  function getTemplate() : QueryTemplate;

  /**
   * Prepare lazy insert template and
   * 
   * @param  string $tableName- name of table
   * @param  array<mixed> $fields - fields
   * @param  int $stagesCount - stages count
   * @return QueryTemplate - prepared statement object
   */
  function getInsertTemplate(string $tableName,
                             array $fields,
                             int $stagesCount = 1): QueryTemplate;
}