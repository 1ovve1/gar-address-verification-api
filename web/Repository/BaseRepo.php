<?php

declare(strict_types=1);

namespace GAR\Repository;

use DB\ORM\Table\SQL\QueryModel;

/**
 * Basic repository class, contains production database
 */
class BaseRepo
{
    /**
     * @param QueryModel $database - production database
     */
    public function __construct(
        private readonly QueryModel $database
    ) {
    }

    /**
     * Return production database accessor $database
     *
     * @return QueryModel
     */
    public function getDatabase(): QueryModel
    {
        return $this->database;
    }
}
