<?php

declare(strict_types=1);

namespace GAR\Storage;

use GAR\Models\Database;

/**
 * Basic repository class, contains production database
 */
class BaseStorage
{
	protected readonly Database $db;
	protected int $regionContext;


    public function __construct()
    {
		$this->regionContext = 1;
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

	/**
	 * @param int $region
	 */
	public function setRegionContext(int $region): void
	{
		$this->regionContext = $region;
	}

	/**
	 * @return int $region
	 */
	public function getRegionContext(): int
	{
		return $this->regionContext;
	}
}
