<?php declare(strict_types=1);

namespace DB\ORM\Migration\Options\Rollback;

use DB\Exceptions\BadQueryResultException;
use DB\ORM\DBAdapter\DBAdapter;
use DB\ORM\Migration\Container\QueryGenerator;
use DB\ORM\Migration\Options\BaseOptionFacade;

class RollbackImpl extends BaseOptionFacade implements Rollback
{
	/**
	 * @inheritDoc
	 * @throws BadQueryResultException
	 * @throws BadQueryResultException
	 */
	static function deleteTable(DBAdapter $db, array|string $tableName): void
	{
		if (is_array($tableName)) {
			$tmpTableNames = [];
			foreach ($tableName as $table) {
				if (false === parent::isTableExists($db, $table)) {
					echo sprintf(
						'Table %s are not exists' . PHP_EOL, $table
					);
				} else {
					$tmpTableNames[] = $table;
				}
			}

			$tableName = implode(', ', $tmpTableNames);
		}

		if (!empty($tableName)) {
			$container = QueryGenerator::genDropTableQuery($tableName);

			self::executeContainer($db, $container);
		}
	}

	/**
	 * @inheritDoc
	 * @throws BadQueryResultException
	 */
	static function deleteTableFromMigrateAble(DBAdapter $db, array|string $className): void
	{
		if (is_array($className)) {
			$tmpTableNames = [];
			foreach ($className as $class) {
				parent::checkMigrateAble($class);
				$tmpTableNames[] = parent::genTableNameFromClassName($class);
			}

			$tableName = $tmpTableNames;
		} else {
			$tableName = parent::genTableNameFromClassName($className);
		}

		self::deleteTable($db, $tableName);
	}
}