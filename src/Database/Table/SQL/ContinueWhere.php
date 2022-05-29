<?php

namespace GAR\Database\Table\SQL;

/**
 * Continue where interface
 *
 * @phpstan-import-type DatabaseContract from \GAR\Database\DBAdapter\DBAdapter
 */
interface ContinueWhere
{
  /**
   * Create AND WHERE template
   * 
   * @param  string $field - name of field
   * @param  string $sign - sign for compare
   * @param  DatabaseContract $value - value to compare
   * @return ContinueWhere
   */
  function andWhere(string $field, string $sign, mixed $value) : ContinueWhere;

  /**
   * Create OR WHERE template
   * 
   * @param  string $field - name of field
   * @param  string $sign - sign for compare
   * @param  DatabaseContract $value - value to compare
   * @return ContinueWhere
   */
  function orWhere(string $field, string $sign, mixed $value) : ContinueWhere;

  /**
   * Creating ORDER BY template
   * 
   * @param  string $field - field to sort 
   * @param  bool $asc - type of sort
   * @return EndQuery
   */
  function orderBy(string $field, bool $asc = true) : EndQuery;

  /**
   * Create LIMIT $count template
   * @param  positive-int $count - limit count
   * @return EndQuery
   */
  function limit(int $count) : EndQuery;

  /**
   * Reset query buffer
   * @return QueryModel
   */
  function reset() : QueryModel;

  /**
   * Save and execute query
   * 
   * @return array<string, mixed>
   */
  function save() : array;

  /**
   * Create template with name $name
   * 
   * @param  string $name - name of template
   * @return void
   */
  function name(string $name) : void;
  
  /**
   * Check if template with name $checkName exists
   * @param  string $checkName - name of template
   * @return bool
   */
  function nameExist(string $checkName) : bool;

  /**
   * Execute template with name $templateName by $values
   * @param  array<DatabaseContract> $values - values to execute
   * @param  string|null $templateName - name of template
   * @return array<string, mixed>
   */
  function execute(array $values, ?string $templateName = null) : array;
}