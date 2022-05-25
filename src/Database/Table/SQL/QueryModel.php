<?php declare(strict_types=1);

namespace GAR\Database\Table\SQL;

interface QueryModel
{
  function insert(array $values, ?string $tableName = null) : EndQuery;
  function forceInsert(array $values) : EndQuery;
  function update(string $field, string|int $value, ?string $tableName = null) : UpdateQuery;
  function delete(?string $tableName = null) : DeleteQuery;
  function select(array $fields, ?array $anotherTables = null) : SelectQuery;
  function findFirst(string $field, string|int $value, ?string $anotherTable = null): array;

  function nameExist(string $checkName) : bool;
  function execute(array $values, ?string $templateName = null) : array;
}