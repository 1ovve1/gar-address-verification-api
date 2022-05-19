<?php declare(strict_types=1);

namespace GAR\Database\Table\Container;

interface Query
{
  function getType() : QueryTypes;
  function getRawSql() : string;
  function isValid() : bool;
}