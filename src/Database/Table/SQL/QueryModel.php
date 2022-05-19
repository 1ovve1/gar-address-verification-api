<?php declare(strict_types=1);

namespace GAR\Database\Table\SQL;

interface QueryModel
{
  function insert(array $values) : EndQuery;
  function forceInsert(array $values) : EndQuery;
  function update(string $field, string $value) : UpdateQuery;
  function delete() : DeleteQuery;
  function select(array $fields, ?array $anotherTables = null) : SelectQuery;
}