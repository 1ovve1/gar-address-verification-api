<?php declare(strict_types=1);

namespace DB\ORM\Migration;

use DB\ORM\DBAdapter\DBAdapter;
use DB\ORM\DBAdapter\QueryResult;
use DB\ORM\DBFacade;
use DB\ORM\Migration\Container\{Query, QueryGenerator};
use RuntimeException;

/**
 * Meta table object, that doing all manipulation like creating table, get meta data and other
 *
 */
class MetaTable implements Migration
{
	/**
	 * @param DBAdapter $db - database adapter connection
	 */
	private function __construct(
		/**
		 * @var DBAdapter - database object
		 */
		private readonly DBAdapter $db
	) {}

	/**
	 * @inheritDoc
	 */
	static function migrate(DBAdapter $db, string $tableName, array $paramsToCreate = []): bool
	{
		if (self::tableExistsCheck($db, $tableName)) {
			echo sprintf('Table %s already exists' . PHP_EOL, $tableName);
			return false;
		}

		$container = QueryGenerator::genCreateTableQuery($tableName, $paramsToCreate);
		return self::executeContainer($db, $container);
	}

	/**
	 * @inheritDoc
	 */
	static function migrateFromMigrateAble(DBAdapter $db, string $className): bool
	{
		self::checkMigrateAble($className);

		$tableName = DBFacade::genTableNameByClassName($className);
		$paramsToCreate = call_user_func($className . '::migrationParams');

		if (self::tableExistsCheck($db, $tableName)) {
			echo sprintf('Table %s already exists' . PHP_EOL, $tableName);
			return false;
		}

		$container = QueryGenerator::genCreateTableQuery(
			$tableName, $paramsToCreate
		);

		return self::executeContainer($db, $container);
	}

	/**
	 * @inheritDoc
	 */
	static function createImmutable(DBAdapter $db): Migration
	{
		return new self($db);
	}

	/**
	 * @inheritDoc
	 */
	function doMigrate(string $tableName, array $paramsToCreate = []): bool
	{
		return self::migrate($this->db, $tableName, $paramsToCreate);
	}

	/**
	 * @inheritDoc
	 */
	function doMigrateFromMigrateAble(string $className): bool
	{
		return self::migrateFromMigrateAble($this->db, $className);
	}

	private static function executeContainer(DBAdapter $db, Query $container): bool
	{
		try {
			$db->rawQuery($container);
		} catch(RuntimeException $exception) {
			echo sprintf('cant create table by this SQL: %s (maybe table already exists)' . PHP_EOL, $container->getRawSQL());

			return false;
		}
		return true;
	}

	/**
	 * Check if the class name implements a MigrateAbleInterface
	 *
	 * @param string $className
	 */
	private static function checkMigrateAble(string $className) : void
	{
		$classExists = class_exists($className);
		$migrateAble = is_a($className, MigrateAble::class, true);
		if (false === ($classExists && $migrateAble)) {
			throw new RuntimeException(sprintf('class %s should implement %s for migration', $className, MigrateAble::class));
		}
	}


	/**
	 * Check table existing and ask user to drop it if exist
	 *
	 * @param DBAdapter $db
	 * @param string $tableName - name of table
	 * @return bool
	 */
	private static function tableExistsCheck(DBAdapter $db, string $tableName): bool
	{
		$container = QueryGenerator::genShowTableQuery();
		$tableList = $db->rawQuery($container)->fetchAll(QueryResult::PDO_F_COL);


		if (!is_array($tableList)) {
			throw new RuntimeException('MetaTable error: $tableList should return array, ' . gettype($tableList) . " given");
		}

		return in_array($tableName, $tableList, true);
	}
}
