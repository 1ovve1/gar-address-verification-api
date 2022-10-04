<?php

declare(strict_types=1);

namespace DB\ORM\Migration\Container;

use DB\Exceptions\Unchecked\BadQueryResultException;
use DB\ORM\QueryBuilder\QueryBuilder;

/**
 * Generator, that implements QueryFactory
 */
class QueryGenerator implements QueryFactory
{
    /**
     * Generate query container manually
     *
     * @param string $query - query string
     * @param callable|null $validate - validate callback
     * @return Query
     */
    public static function customQuery(string $query, ?callable $validate = null): Query
    {
        return (new QueryObject())
      ->setRawSql($query)
      ->validate($validate ?? fn () => true);
    }

    /**
     * Generate describe query (probably work only in mysql?)
     * @param string $tableName - name of table
     * @return Query - query object
     */
    public static function genMetaQuery(string $tableName): Query
    {
        return (new QueryObject())
      ->setRawSql(self::makeMetaQuery($tableName))
      ->setType(QueryTypes::META)
      ->validate(fn () => true);
    }

    /**
     * {@inheritDoc}
     */
    public static function genCreateTableQuery(string $tableName,
                                               array $params): Query
    {
        return (new QueryObject())
	      ->setType(QueryTypes::META)
	      ->setRawSql(self::makeCreateTableQuery($tableName, $params))
	      ->validate(fn () => true);
    }

    /**
     * Return show tables query
     * @return Query - query object
     */
    public static function genShowTableQuery(): Query
    {
		$query = match ($_ENV['DB_TYPE']) {
			'mysql' => 'SHOW TABLES',
			'pgsql' => "SELECT `table_name` FROM `information_schema`.`tables` WHERE `table_schema` = 'public' ORDER BY `table_name`;",
			default => throw new BadQueryResultException("Unknown db type '{$_ENV['DB_TYPE']}'")
		};
        return (new QueryObject())
	      ->setType(QueryTypes::META)
	      ->setRawSql($query)
	      ->validate(fn () => true);
    }

	/**
	 * Generate DROP TABLE SQL query by $tableName
	 * @param string $tableName
	 * @return Query
	 */
	static function genDropTableQuery(string $tableName): Query
	{
		return (new QueryObject())
			->setType(QueryTypes::META)
			->setRawSql(self::makeDropTable($tableName))
			->validate(fn () => true);
	}

	/**
	 * Make DROP TABLE SQL query
	 * @param string $tableName
	 * @return string
	 */
	protected static function makeDropTable(string $tableName): string
	{
		return sprintf("DROP TABLE %s", $tableName);
	}

    /**
     * Make meta query (describe)
     * @param string $tableName - name of table
     * @return string - query string
     */
    public static function makeMetaQuery(string $tableName): string
    {
        return sprintf(
            'DESCRIBE %s',
            $tableName
        );
    }

    /**
     * Make create table if exists query string
     * @param string $tableName - name of table
     * @param MigrationParams $fieldsWithParams - fields with params
     * @return string - query string
     */
    public static function makeCreateTableQuery(string $tableName, array $fieldsWithParams): string
    {
        $formattedFields = [
			'fields' => '',
	        'foreign' => '',
        ];

        foreach ($fieldsWithParams as $type => $params) {

            $formattedFields[$type] = match($type) {
	            'fields' =>  self::parseFieldsParams($params),
	            'foreign' => self::parseForeignParams($params),
			};
        }

        return sprintf(
            'CREATE TABLE `%1$s` (%2$s%3$s)',
            $tableName,
            $formattedFields['fields'],
	        (empty($formattedFields['foreign'])) ? '': ', ' . $formattedFields['foreign']
        );
    }

	/**
	 * @param array<string, string> $params
	 * @return string
	 */
	protected static function parseFieldsParams(array $params): string
	{
		$result = '';

		foreach ($params as $index => $param) {
			$param = strtoupper($param);

			switch ($_ENV['DB_TYPE']) {
				case 'mysql':
					break;
				case 'pgsql':
					$param = str_replace('TINYINT', 'SMALLINT', $param);
					$param = str_replace('UNSIGNED', '', $param);
					if (str_contains($param, 'AUTO_INCREMENT')) {
						$param = str_replace('AUTO_INCREMENT', '', $param);
						$param = str_replace('SMALLINT', 'SERIAL', $param);
						$param = str_replace('INT', 'SERIAL', $param);
						$param = str_replace('BIGINT', 'SERIAL', $param);
					}
					break;
			}
			$result .= sprintf(
				"`%s` %s, ",
				$index,
				$param
			);
		}

		return substr($result, 0, -2);
	}

	/**
	 * @param array<string, string|String[]> $params
	 * @return string
	 */
	protected static function parseForeignParams(array $params): string
	{
		$result = '';

		foreach ($params as $index => $param) {
			if (is_array($param)) {
				$param = self::handleForeignKeyArrayParam($param);
			}

			$result .= sprintf(
				'FOREIGN KEY (`%s`) REFERENCES %s, ',
				$index,
				$param
			);
		}

		return substr($result, 0, -2);
	}

	/**
	 * @param String[] $param
	 * @return string
	 */
	private static function handleForeignKeyArrayParam(array $param): string
	{
		if(count($param) === 2 && is_a(current($param), QueryBuilder::class, true)) {
			[$className, $field] = array_values($param);
			$callable = $className . '::table';

			if (is_callable($callable)) {
				return sprintf(
					'`%s` (`%s`)',
					$callable(), $field
				);
			} else {
				throw new \RuntimeException("Callable not found ('{$callable}')");
			}

		}

		echo "incorrect syntax: you should use 'foreign' => ['field' => [ClassName::class, 'foreign_field']] template " .
			 "but we have: " . PHP_EOL;

		var_dump($param);
		exit('abort');
	}
}
