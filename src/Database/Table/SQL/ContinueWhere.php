<?php

namespace GAR\Database\Table\SQL;

interface ContinueWhere
{
  function andWhere(string $field, string $sign, int|string $value) : ContinueWhere;
  function orWhere(string $field, string $sign, int|string $value) : ContinueWhere;
  function orderBy(string $field, bool $asc = true) : endQuery;

  function nameExist(string $checkName) : bool;
  function name(string $name) : string;
  function reset() : QueryModel;
  function save() : array;
  function execute(array $values, ?string $templateName = null) : array;
}