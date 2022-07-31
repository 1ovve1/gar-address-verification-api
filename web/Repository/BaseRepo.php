<?php

declare(strict_types=1);

namespace GAR\Repository;

use DB\ORM\Table\SQL\QueryModel;

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
