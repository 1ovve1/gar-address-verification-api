<?php

namespace GAR\Database\DBAdapter;

/**
 * Common query tempalte interface for prepared statements
 *
 * @phpstan-import-type DatabaseContract from DBAdapter
 */
interface QueryTemplate
{
  /**
   * Execute statement
   * 
   * @param  array<DatabaseContract> $values - values to execute
   * @return mixed
   */
  function exec(array $values) : mixed;

  /**
   * Accept changes in template (use for lazy insert)
   * 
   * @return mixed
   */
  function save(): mixed;
}