<?php

namespace GAR\Database\DBAdapter;

use GAR\Database\Table\Container\Query;

interface DBAdapter
{
  function rawQuery(Query $query) : self;
  function fetchAll(int $flag) : mixed;
  function prepare(string $template) : self;
  function execute(array $values) : self;
  function getTemplate() : mixed;
  function getInsertTemplate(string $tableName,
                             array $fields,
                             int $stagesCount = 1): QueryTemplate;
}