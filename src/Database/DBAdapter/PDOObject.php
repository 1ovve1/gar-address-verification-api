<?php declare(strict_types=1);

namespace GAR\Database\DBAdapter;

use GAR\Database\Table\Container\Query;
use RuntimeException;
use PDO;
use PDOStatement;

/**
 * Implemets DBAdapter for PDO conncetion type
 *
 * @phpstan-import-type DatabaseContract from DBAdapter
 */
class PDOObject implements DBAdapter
{
  /**
   * @var PDO $instance - curr instance of db connection
   */
  private PDO $instance;
  /**
   * @var PDOStatement|null $lastQuery - contains last result of query method
   */
  private ?PDOStatement $lastQuery = null;


  /**
   * @param string $dbType - type name of curr db
   * @param string $dbHost - db host
   * @param string $dbName - db name
   * @param string $dbPort - port
   */
  function __construct(
    private readonly string $dbType,
    private readonly string $dbHost,
    private readonly string $dbName,
    private readonly string $dbPort,
  )
  {}

  /**
   * Realize connect via PDO by username and password
   * 
   * @param string $dbUsername - name of user to connect
   * @param string $dbPass - pass from curr db
   * @return void
   */
  public function connect(string $dbUsername, string $dbPass) : void
  {
    $dsn = sprintf(
      '%s:host=%s;dbname=%s;port=%s;charset=utf8',
      $this->dbType,
      $this->dbHost,
      $this->dbName,
      $this->dbPort,
    );

    $this->setInstance(new PDO(
      $dsn,
      $dbUsername, $dbPass,
      [PDO::ATTR_PERSISTENT => true, PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true]
    ));
  }

  /**
   * Make SQL query by $query container
   * 
   * @param Query $query - query container
   * @return self - self
   */
  public function rawQuery(Query $query) : self
  {
    if ($query->isValid()) {
      $res = $this->getInstance()->query($query->getRawSql());
      $this->setLastQuery($res);
    } else {
      throw new RuntimeException(
        "PDOObject error: invalid sql query '" . $query->getRawSql() . "'"
      );
    }

    return $this;
  }

  /**
   * Prepare template
   *
   * @param string $template - string template query
   * @return self - self
   */
  function prepare(string $template): self
  {
    $template = $this->getInstance()->prepare($template);
    $this->setLastQuery($template);
    return $this;
  }

  /**
   * Execute prepare statement
   *
   * @param array<DatabaseContract> $values- values to execute
   * @return self - self
   */
  function execute(array $values): DBAdapter
  {
    $this->getLastQuery()->execute($values);
    return $this;
  }

  /**
   * Return last template statement
   * @return QueryTemplate
   */
  function getTemplate(): QueryTemplate
  {
    if (is_null($this->lastQuery)) {
      throw new RuntimeException('PDOObject (DBAdapter) error: template dosent exists');
    }
    return new PDOTemplate($this->lastQuery);
  }


  /**
   * Return InsertTemplate
   * 
   * @param string $tableName - name of table
   * @param array<string> $fields - fields to prepare
   * @param int $stagesCount - buffer size
   * @return QueryTemplate - prepared lazy insert object
   */
  function getInsertTemplate(string $tableName,
                             array $fields,
                             int $stagesCount = 1) : QueryTemplate
  {
    return new PDOLazyInsertTemplate($this, $tableName, $fields, $stagesCount);
  }


  /**
   * Fething last query by $flag
   * 
   * @param int $flag - standard PDO flag
   * @return array<string, mixed>|bool|null - fetch result
   */
  public function fetchAll(int $flag = DBAdapter::PDO_F_ALL) : array|bool|null
  {
    return $this->getLastQuery()->fetchAll($flag);
  }

  /**
   * Return last query (PDOStatement)
   * @return PDOStatement
   */
  private function getLastQuery(): PDOStatement
  {
    if (is_null($this->lastQuery)) {
      throw new RuntimeException('PDOObject (DBAdapter) error: call to undefined PDOStatement');
    }
    return $this->lastQuery;
  }

  /**
   * Set last query by $lastQuery
   * @param PDOStatement|bool $lastQuery - query statement
   * @throws RuntimeException
   */
  private function setLastQuery(PDOStatement|bool $lastQuery): void
  {
    if (!is_bool($lastQuery)) {
      $this->lastQuery = $lastQuery;
    } else {
      throw new RuntimeException(
        'PDOObject error: bad query'
      );
    }
  }

  /**
   * Set instance by PDO object
   * @param PDO|null $connection - ready PDO object
   * @throws RuntimeException
   * @return void
   */
  private function setInstance(?PDO $connection): void
  {
    if (is_null($connection)) {
      throw new RuntimeException('PDOObject (DBAdapter) error: PDO instance is null');
    }
    $this->instance = $connection;
  }

  /**
   * Return curr instance of PDO
   * @return PDO
   * @throws RuntimeException
   */
  private function getInstance(): PDO
  {
    if (!isset($this->instance)) {
      throw new RuntimeException('PDOObject (DBAdapter) error: call to undefined PDO object');
    }
    return $this->instance;
  }
}