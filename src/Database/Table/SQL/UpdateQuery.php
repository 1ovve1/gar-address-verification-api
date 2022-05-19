<?php

namespace GAR\Database\Table\SQL;

interface UpdateQuery
{
  function where(string $field, string $sign, int|string $value) : ContinueWhere;
  function save() : array;
  function reset() : QueryModel;
}