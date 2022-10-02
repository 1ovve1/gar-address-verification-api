<?php declare(strict_types=1);

namespace DB\ORM\Migration\Options\Migrate;

use DB\ORM\DBAdapter\DBAdapter;
use DB\ORM\Migration\Container\QueryGenerator;
use DB\ORM\Migration\Options\BaseOptionFacade;
use RuntimeException;

class MigrateImpl extends BaseOptionFacade implements Migrate
{
	/**
	 * @inheritDoc
	 */
	static function migrate(DBAdapter $db, string $tableName, array $paramsToCreate): void
	{
		if (true === parent::isTableExists($db, $tableName)) {
			echo sprintf('Table %s already exists' . PHP_EOL, $tableName);
			return;
		}

		$container = QueryGenerator::genCreateTableQuery($tableName, $paramsToCreate);

		try {
			parent::executeContainer($db, $container);
		} catch (RuntimeException) {
			exit(sprintf(
				'Cant create table by this SQL: %s (maybe table already exists)' . PHP_EOL,
				$container->getRawSQL()
			));
		}
	}

	/**
	 * @inheritDoc
	 */
	static function migrateFromMigrateAble(DBAdapter $db, string $className): void
	{
		parent::checkMigrateAble($className);

		$tableName = parent::genTableNameFromClassName($className);
		$callable = $className . '::migrationParams';

		if (is_callable($callable)) {
			$paramsToCreate = $callable();
		} else {
			throw new RuntimeException("Params not found (function '{$callable}' not found");
		}

		self::migrate($db, $tableName, $paramsToCreate);
	}


}