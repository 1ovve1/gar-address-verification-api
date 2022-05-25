<?php

namespace GAR\Database\DBAdapter;

interface QueryTemplate
{
  function exec(array $values) : mixed;
  function save(): mixed;
}