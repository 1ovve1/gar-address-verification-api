<?php declare(strict_types=1);

namespace GAR\Database\Table\SQL;

interface EndQuery
{
  function save(): array;
  function reset(): QueryModel;
}