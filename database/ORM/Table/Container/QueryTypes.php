<?php

declare(strict_types=1);

namespace DB\ORM\Table\Container;

/**
 * Query types enum
 */
enum QueryTypes
{
    case INSERT;
    case SELECT;
    case META;
}
