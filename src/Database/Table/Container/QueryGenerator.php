<?php

declare(strict_types=1);

namespace GAR\Database\Table\Container;

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
     * Generate create table if exists query
     *
     * @param string $tableName - name of table
     * @param array<string, string> $fieldsWithParams - fields and their params
     * @return Query - query object
     */
    public static function genCreateTableQuery(
        string $tableName,
        array $fieldsWithParams
    ): Query {
        return (new QueryObject())
      ->setType(QueryTypes::META)
      ->setRawSql(self::makeCreateTableQuery($tableName, $fieldsWithParams))
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
     * @param array<string, string> $fieldsWithParams - fields with params
     * @return string - query string
     */
    public static function makeCreateTableQuery(string $tableName, array $fieldsWithParams): string
    {
        $formattedFields = [];

        foreach ($fieldsWithParams as $field => $params) {
            if (empty($params)) {
                throw new InvalidArgumentException(sprintf(
                    "field %s should contains type params!",
                    $field
                ));
            }
            $formattedFields[] = sprintf(
                "%s %s",
                $field,
                $params
            );
        }
        return sprintf(
            'DROP TABLE IF EXISTS %1$s; CREATE TABLE %1$s (%2$s)',
            $tableName,
            implode(', ', $formattedFields)
        );
    }
}
