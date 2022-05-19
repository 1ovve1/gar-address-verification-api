<?php declare(strict_types=1);

namespace GAR\Database\DBAdapter;

use GAR\Database\Table\Container\Query;
use RuntimeException;
use PDO;
use PDOStatement;

/**
 * Contains all parameters of current connection
 */
class PDOObject implements DBAdapter
{
  /**
   * @var PDO|null - curr instance of db connection
   */
  private ?PDO $instance = null;
  /**
   * @var PDOStatement|null - contains last result of query method
   */
  private ?PDOStatement $lastQuery = null;

  public const F_ALL = PDO::FETCH_ASSOC;
  public const F_COL = PDO::FETCH_COLUMN;


  /**
   * @param string $dbType - type of curr db
   * @param string $dbHost - db host
   * @param string $dbName - curr db
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
   * Realize connect via PDO by password
   * @param string $dbUsername - name of user to connect
   * @param string $dbPass - pass from curr db
   * @return void
   */
  public function connect(string $dbUsername, string $dbPass) : void
  {
    if (is_null($this->getInstance())) {
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
      ));
    }
  }

  /**
   * Make SQL query
   * @param Query $query - sql object
   * @return self
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
   * @param string $template - string template query
   * @return PDOStatement - pdo object
   */
  function prepare(string $template): PDOStatement
  {
    return $this->getInstance()->prepare($template);
  }

  /**
   * Return prepared object InsertTemplate
   * @param string $tableName - name of table
   * @param array $fields - fields to prepare
   * @param int $stagesCount - buffer size
   * @return InsertTemplate - prepare object
   */
  function getInsertTemplate(string $tableName,
                             array $fields,
                             int $stagesCount = 1) : InsertTemplate
  {
    return new PDOTemplate($tableName, $fields, $stagesCount);
  }


  /**
   * @param int $flag - standard PDO flag
   * @return array|bool|null - fetch result
   */
  public function fetchAll(int $flag = self::F_ALL) : array|bool|null
  {
    return $this->getLastQuery()?->fetchAll($flag);
  }

  /**
   * @return PDOStatement|null
   */
  private function getLastQuery(): ?PDOStatement
  {
    return $this->lastQuery;
  }

  /**
   * @param PDOStatement|null $lastQuery
   */
  private function setLastQuery(?PDOStatement $lastQuery): void
  {
    if ($lastQuery) {
      $this->lastQuery = $lastQuery;
    } else {
      throw new RuntimeException(
        'PDOObject error: bad query'
      );
    }
  }

  /**
   * Set instance by PDO object
   * @param PDO $connection - ready PDO object
   * @return void
   */
  private function setInstance(PDO $connection): void
  {
    $this->instance = $connection;
  }

  /**
   * @return PDO|null - curr instance of PDO object
   */
  private function getInstance(): PDO|null
  {
    return $this->instance;
  }
}