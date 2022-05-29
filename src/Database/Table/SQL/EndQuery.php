<?php declare(strict_types=1);

namespace GAR\Database\Table\SQL;

/**
 * End query interface
 *
 * @phpstan-import-type DatabaseContract from \GAR\Database\DBAdapter\DBAdapter
 */
interface EndQuery
{
  /**
   * Creating ORDER BY template 
   * 
   * @param  string $field - name of field
   * @param  bool|boolean $asc - type of sort
   * @return EndQuery
   */
  function orderBy(string $field, bool $asc = true) : EndQuery;

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