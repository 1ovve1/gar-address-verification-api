<?php declare(strict_types=1);

namespace GAR\Database;


use GAR\Database\DBAdapter\DBAdapter;
use GAR\Database\DBAdapter\PDOObject;
use GAR\Logger\Log;
use GAR\Logger\Msg;
use InvalidArgumentException;
use PDOException;

/**
 * DB FACADE CLASS
 * 
 * SINGLETON, FULL-STATIC FACADE
 * USE FOR GETTING ACTUAL BD CONNECTION
 */
class DBFacade
{
	/**
	 * @var DBAdapter|null - PDO object
   */
	public static ?DBAdapter $instance = null;

  /**
   *  Get curr instance of database
   * @return DBAdapter - PDO-object with curr db connection
   */
	public static function getInstance() : DBAdapter
  {

		if (self::$instance === null) {
			self::$instance = self::connectViaPDO();
		} 

		return self::$instance;
	}


  /**
   *  Method to connection with database using
   *  Env.php file
   * @return PDOObject connected PDO-object
   */
	public static function connectViaPDO() : PDOObject
	{
    $PDO = new PDOObject(
      $_SERVER['DB_TYPE'],
      $_SERVER['DB_HOST'],
      $_SERVER['DB_NAME'],
      $_SERVER['DB_PORT'],
    );

		try {
			Log::write(Msg::LOG_DB_INIT->value);
      $PDO->connect($_SERVER['DB_USER'], $_SERVER['DB_PASS']);
			Log::write(Msg::LOG_COMPLETE->value);
		} catch (PDOException $exception) {
			Log::error(
				$exception,
        $_SERVER
			);
		}

    return $PDO;
	}

  public static function genTableNameByClassName(string $className) : string
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

    if (!preg_match('/^[a-zA-Z][a-zA-Z_]{1,18}$/',$tableName)) {
      throw new InvalidArgumentException('invalid table name :' . $tableName);
    }

    return $tableName;
  }
}