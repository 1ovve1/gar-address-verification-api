<?php

declare(strict_types=1);

namespace GAR\Repository;

use DB\Models\Database;

/**
 * Basic repository class, contains production database
 */
class BaseRepo
{
	protected readonly Database $db;

    public function __construct(
    ) {
		$this->db = Database::getInstance();
    }

    /**
     * Return production database accessor $database
     *
     */
    public function getDatabase(): Database
    {
		return $this->db;
    }
}
