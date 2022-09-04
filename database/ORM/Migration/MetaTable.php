<?php declare(strict_types=1);

namespace DB\ORM\Migration;

use DB\Exceptions\BadQueryResultException;
use DB\ORM\DBAdapter\DBAdapter;
use DB\ORM\Migration\Options\Migrate\{Migrate, MigrateImpl};
use DB\ORM\Migration\Options\Rollback\{Rollback, RollbackImpl};

/**
 * Meta table object, that doing all manipulation like creating table, get metadata and other
 *
 */
class MetaTable implements Migrate, MigrateImmutable, Rollback, RollbackImmutable
{
	/**
	 * @param DBAdapter $db - database adapter connection
	 */
	private function __construct(
		/**
		 * @var DBAdapter $db - database object
		 */
		private readonly DBAdapter $db
	) {}

	/**
	 * Create immutable object of MetaTable
	 * @param DBAdapter $db
	 * @return MetaTable
	 */
	static function createImmutable(DBAdapter $db): self
	{
		return new self($db);
	}

	/**
	 * @inheritDoc
	 * @throws BadQueryResultException
	 */
	static function migrate(DBAdapter $db, string $tableName, array $paramsToCreate = []): void
	{
		MigrateImpl::migrate($db, $tableName, $paramsToCreate);
	}

	/**
	 * @inheritDoc
	 * @throws BadQueryResultException
	 */
	static function migrateFromMigrateAble(DBAdapter $db, string $className): void
	{
		MigrateImpl::migrateFromMigrateAble($db, $className);
	}

	/**
	 * @inheritDoc
	 * @throws BadQueryResultException
	 */
	function doMigrate(string $tableName, array $paramsToCreate = []): void
	{
		self::migrate($this->db, $tableName, $paramsToCreate);
	}

	/**
	 * @inheritDoc
	 * @throws BadQueryResultException
	 */
	function doMigrateFromMigrateAble(string $className): void
	{
		self::migrateFromMigrateAble($this->db, $className);
	}

	/**
	 * @inheritDoc
	 * @throws BadQueryResultException
	 */
	static function deleteTable(DBAdapter $db, array|string $tableName): void
	{
		RollbackImpl::deleteTable($db, $tableName);
	}

	/**
	 * @inheritDoc
	 * @throws BadQueryResultException
	 */
	static function deleteTableFromMigrateAble(DBAdapter $db, array|string $className): void
	{
		RollbackImpl::deleteTableFromMigrateAble($db, $className);
	}

	/**
	 * @inheritDoc
	 * @throws BadQueryResultException
	 */
	function doDeleteTable(array|string $tableName): void
	{
		self::deleteTable($this->db, $tableName);
	}

	/**
	 * @inheritDoc
	 * @throws BadQueryResultException
	 */
	function doDeleteTableFromMigrateAble(array|string $className): void
	{
		self::deleteTableFromMigrateAble($this->db, $className);
	}
}
