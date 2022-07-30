<?php

declare(strict_types=1);

namespace DB\ORM;

use DB\ORM\DBAdapter\DBAdapter;
use DB\ORM\DBAdapter\PDO\PDOObject;
use InvalidArgumentException;
use PDOException;

/**
 * Database facade static class
 */
class DBFacade
{
    /**
     * @var DBAdapter|null - database static object
   */
    public static ?DBAdapter $instance = null;

    /**
     * Get curr instance of database
     *
     * @return DBAdapter
     */
    public static function getInstance(): DBAdapter
    {
        if (self::$instance === null) {
            self::$instance = self::connectViaPDO();
        }

        return self::$instance;
    }


    /**
     * Connection via PDO
     *
     * @return PDOObject
     */
    public static function connectViaPDO(): PDOObject
    {
        $PDO = new PDOObject(
            $_SERVER['DB_TYPE'],
            $_SERVER['DB_HOST'],
            $_SERVER['DB_NAME'],
            $_SERVER['DB_PORT'],
        );

        $PDO->connect($_SERVER['DB_USER'], $_SERVER['DB_PASS']);

        return $PDO;
    }

    /**
     * Generate table name in snake_case
     * @param  class-string $className - full class name namespace
     * @return string
     */
    public static function genTableNameByClassName(string $className): string
    {
        // remove some ..\\..\\..\\ClassName prefix
        $arrStr = explode('\\', $className);
        $className = end($arrStr);

        $tableName = '';
        foreach (str_split(strtolower($className)) as $key => $char) {
            if ($key !== 0 && ctype_upper($className[$key])) {
                $tableName .= '_';
            }
            $tableName .= $char;
        }

        if (!preg_match('/^[a-zA-Z][a-zA-Z_]{1,18}$/', $tableName)) {
            throw new InvalidArgumentException('invalid table name :' . $tableName);
        }

        return $tableName;
    }
}
