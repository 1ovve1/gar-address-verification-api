<?php

declare(strict_types=1);

namespace DB\ORM\QueryBuilder\Container;

/**
 * Query types enum
 */
enum QueryTypes
{
    case INSERT;
    case SELECT;
    case META;
}
