<?php

namespace GAR\Database\DBAdapter;

use GAR\Database\Table\Container\Query;

interface DBAdapter
{
  function rawQuery(Query $query) : self;
  function fetchAll(int $flag) : mixed;
  function prepare(string $template) : mixed;
  function getInsertTemplate(string $tableName,
                             array $fields,
                             int $stagesCount = 1): InsertTemplate;
}