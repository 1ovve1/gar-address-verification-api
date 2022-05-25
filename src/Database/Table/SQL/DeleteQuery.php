<?php

namespace GAR\Database\Table\SQL;

interface DeleteQuery
{
  function where(string $field, string $sign, int|string $value) : ContinueWhere;

  function nameExist(string $checkName) : bool;
  function name(string $name) : string;
  function reset(): QueryModel;
  function execute(array $values, ?string $templateName = null) : array;
  function save(): array;
}