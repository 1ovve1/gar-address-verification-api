<?php

namespace GAR\Database\DBAdapter;

interface InsertTemplate
{
  function exec(DBAdapter $db, array $values) : mixed;
  function save(DBAdapter $db): mixed;
}