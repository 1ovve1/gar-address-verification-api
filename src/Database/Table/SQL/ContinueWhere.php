<?php

namespace GAR\Database\Table\SQL;

interface ContinueWhere
{
  function andWhere(string $field, string $sign, int|string $value) : ContinueWhere;
  function orWhere(string $field, string $sign, int|string $value) : ContinueWhere;
  function reset() : QueryModel;
  function save() : array;
}