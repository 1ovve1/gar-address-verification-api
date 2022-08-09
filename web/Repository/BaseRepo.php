<?php

declare(strict_types=1);

namespace GAR\Repository;

use DB\ORM\QueryBuilder\AbstractSQL\QueryModel;

/**
 * Basic repository class, contains production database
 */
class BaseRepo
{
    public function __construct(
    ) {
    }

    /**
     * Return production database accessor $database
     *
     */
    public function getDatabase(): void
    {
    }
}
