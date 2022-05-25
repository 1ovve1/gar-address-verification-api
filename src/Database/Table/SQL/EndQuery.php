<?php declare(strict_types=1);

namespace GAR\Database\Table\SQL;

interface EndQuery
{
  function orderBy(string $field, bool $asc = true) : endQuery;
  function nameExist(string $checkName) : bool;
  function name(string $name) : string;
  function save(): array;
  function execute(array $values, ?string $templateName = null) : array;
  function reset(): QueryModel;
}