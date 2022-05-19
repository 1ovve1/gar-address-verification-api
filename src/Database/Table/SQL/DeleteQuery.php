<?php

namespace GAR\Database\Table\SQL;

interface DeleteQuery
{
  function where(string $field, string $sign, int|string $value) : ContinueWhere;
  function reset(): QueryModel;
  function save(): array;
}