<?php

declare(strict_types=1);

namespace DB\ORM\Migration\Container;

use InvalidArgumentException;

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
        return (new QueryObject())
      ->setType(QueryTypes::META)
      ->setRawSql('SHOW TABLES')
      ->validate(fn () => true);
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
     * @param array<string, array<string, string>> $fieldsWithParams - fields with params
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
	            'fields' =>  self::parseParamsByTemplate('%s %s, ', $params),
	            'foreign' => self::parseParamsByTemplate('FOREIGN KEY (%s) REFERENCES %s, ', $params),
				default => ''
			};
        }

        return sprintf(
            'CREATE TABLE %1$s (%2$s%3$s)',
            $tableName,
            $formattedFields['fields'],
	        (empty($formattedFields['foreign'])) ? '': ', ' . $formattedFields['foreign']
        );
    }

	/**
	 * @param string $template
	 * @param array<string, string> $params
	 * @return string
	 */
	protected static function parseParamsByTemplate(string $template, array $params): string
	{
		$result = '';

		foreach ($params as $index => $param) {
			$result .= sprintf(
				$template,
				$index,
				$param
			);
		}

		return substr($result, 0, -2);
	}
}
