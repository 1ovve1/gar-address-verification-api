<?php declare(strict_types=1);

namespace GAR\Database\Table\SQL;

interface SelectQuery
{
  function where(string $field, string $sign, int|string $value) : ContinueWhere;
  function innerJoin(string $table, array $condition) : SelectQuery;
  function leftJoin(string $table, array $condition) : SelectQuery;
  function rightJoin(string $table, array $condition) : SelectQuery;
  function limit(int $count) : EndQuery;
  function reset(): QueryModel;
}