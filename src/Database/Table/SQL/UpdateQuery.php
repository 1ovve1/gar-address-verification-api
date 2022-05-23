<?php

namespace GAR\Database\Table\SQL;

interface UpdateQuery
{
  function where(string $field, string $sign, int|string $value) : ContinueWhere;

  function nameExist(string $checkName) : bool;
  function name(string $name) : string;
  function save() : array;
  function reset() : QueryModel;
  function execute(array $values, ?string $templateName = null) : array;
}