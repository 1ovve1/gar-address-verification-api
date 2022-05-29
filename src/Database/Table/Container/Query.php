<?php declare(strict_types=1);

namespace GAR\Database\Table\Container;

/**
 * Common query container interface
 */
interface Query
{
  /**
   * Return type of query
   * @return QueryTypes
   */
  function getType() : QueryTypes;

  /**
   * Return raw query string
   * @return string
   */
  function getRawSql() : string;

  /**
   * Check raw query by validation callback
   * @return boolean
   */
  function isValid() : bool;
}