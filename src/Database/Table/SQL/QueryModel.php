<?php declare(strict_types=1);

namespace GAR\Database\Table\SQL;

interface QueryModel
{
  function insert(array $values) : EndQuery;
  function forceInsert(array $values) : EndQuery;
  function update(string $field, string|int $value) : UpdateQuery;
  function delete() : DeleteQuery;
  function select(array $fields, ?array $anotherTables = null) : SelectQuery;
  function findFirst(string $field, string|int $value, ?string $anotherTable = null): array;
}