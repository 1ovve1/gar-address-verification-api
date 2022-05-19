<?php declare(strict_types=1);

namespace GAR\Database\Table\Container;

enum QueryTypes
{
  case INSERT;
  case SELECT;
  case META;
}