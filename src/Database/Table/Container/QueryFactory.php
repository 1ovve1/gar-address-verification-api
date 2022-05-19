<?php declare(strict_types=1);

namespace GAR\Database\Table\Container;

interface QueryFactory
{
  /**
   * Generate query container manually
   *
   * @param string $query - query string
   * @param callable|null $validate - validate callback
   * @return Query
   */
  static function customQuery(string $query,
                              ?callable $validate = null) : Query;

  /**
   * Generate describe query (probably work only in mysql?)
   * @param string $tableName - name of table
   * @return Query - query object
   */
  static function genMetaQuery(string $tableName) : Query;

  /**
   * Generate create table if exists query
   * @param string $tableName - name of table
   * @param array $fieldsWithParams - fields and their params
   * @return Query - query object
   */
  static function genCreateTableQuery(string $tableName,
                               array $fieldsWithParams): Query;

  /**
   * Return show tables query
   * @return Query - query object
   */
  static function genShowTableQuery() : Query;
}