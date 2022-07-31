<?php

declare(strict_types=1);

namespace DB\ORM;

use DB\ORM\DBAdapter\DBAdapter;
use DB\ORM\DBAdapter\PDO\PDOObject;
use \RuntimeException;
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
	    $negStrLen = -strlen($className);
	    $tableName = '';

	    for ($index = -1, $char = $className[$index];
	         $index >= $negStrLen && $char !== '\\';
	         --$index, $char = $className[$index]) {

		    if (ctype_upper($char)) {
			    $tableName = '_' . strtolower($char) . $tableName;
		    } else {
			    $tableName = $char . $tableName;
		    }

	    }

	    return substr($tableName, 1);
    }

	public static function dumpException(mixed $item, string $message, array $params): void
	{
		echo 'Dump of current item...' . PHP_EOL;
		echo '<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' . PHP_EOL;
		var_dump($item);
		echo 'Params:' . PHP_EOL;
		var_dump($params);
		echo '>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>' . PHP_EOL;

		throw new RuntimeException($message);
	}
}
