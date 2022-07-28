<?php

declare(strict_types=1);

namespace GAR\Database\Table\Container;

/**
 * Query types enum
 */
enum QueryTypes
{
    case INSERT;
    case SELECT;
    case META;
}
