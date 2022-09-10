<?php

declare(strict_types=1);

namespace GAR\Storage;

use DB\Models\Database;

/**
 * Basic repository class, contains production database
 */
class BaseStorage
{
	protected readonly Database $db;

    public function __construct()
    {
		$this->db = Database::getInstance();
    }

    /**
     * Return production database accessor $database
     * @return Database
     */
    public function getDatabase(): Database
    {
		return $this->db;
    }
}
