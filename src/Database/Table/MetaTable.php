<?php

namespace GAR\Database\Table;


use GAR\Database\DBAdapter\DBAdapter;
use GAR\Database\Table\Container\QueryFactory;
use GAR\Database\Table\Container\QueryGenerator;
use JetBrains\PhpStorm\ArrayShape;

abstract class MetaTable
{
  /**
   * @var DBAdapter - PDO object
   */
  private readonly DBAdapter $db;
  /**
   * @var string - name of table
   */
  private readonly string $tableName;
  /**
   * @var array - table fields
   */
  private array $fields;
  /**
   * @var array - full information about table
   */
  private readonly array $metaInfo;
  /**
   * @var QueryFactory - factory of sql queries
   */
  private readonly QueryFactory $factory;

  /**
   * Create meta table object
   * @param DBAdapter $db - database adapter connection
   * @param string $tableName - name of table
   * @param array|null $createOption - option for create table
   */
  public function __construct(DBAdapter $db,
                              string $tableName,
                              ?array $createOption = null)
  {
    $this->db = $db;
    $this->tableName = $tableName;
    $this->factory = new QueryGenerator();
    if ($createOption !== null) {
      $this->createTable($tableName, $createOption);
    }

    [$this->metaInfo, $this->fields] = $this->getMetaInfoAndFields($tableName);
  }

  /**
   *  getting meta info from table meta (only for mysql)
   * @param string $tableName name of table (probably $this->name)
   * @return array
   */
  #[ArrayShape(['metaInfo' => "array|false", 'fields' => "array|false"])]
  private function getMetaInfoAndFields(string $tableName) : array
  {
    $query = $this->getFactory()->genMetaQuery($tableName);

    $metaInfo = $this->getDb()->rawQuery($query)->fetchAll($this->getDb()::F_ALL);
    $tableFields = $this->getDb()->rawQuery($query)->fetchAll($this->getDb()::F_COL);

    return [$metaInfo, $tableFields];
  }

  /**
   * Create table using curr connected, name of table and fields
   * @param string $tableName - name of table
   * @param array $fieldsToCreate - fields and their params
   * @return void
   */
  private function createTable(string $tableName, array $fieldsToCreate) : void
  {
    if ($this->tableExistsAndDropCheck($tableName)) {
      return;
    }

    $this->getDb()->rawQuery($this->getFactory()->genCreateTableQuery(
      $this->getTableName(), $fieldsToCreate
    ));
  }

  /**
   * Check table existing and ask user to drop it if exist
   * @param string $tableName - name of table
   * @return bool - user decision
   */
  protected function tableExistsAndDropCheck(string $tableName) : bool
  {
    $connection = $this->getDb();
    $tableList = $connection->rawQuery($this->getFactory()->genShowTableQuery())
                            ->fetchAll($this->getDb()::F_COL);

    return in_array($tableName, $tableList);
  }

  /**
   * Return name of table
   * @return string - name of table
   */
  public function getTableName(): string
  {
    return $this->tableName;
  }

  /**
   * Return mta info about table
   * @return array|null - meta info about table
   */
  protected function getMetaInfo(): ?array
  {
    return $this->metaInfo;
  }

  /**
   * Return fields for curr table
   * @return array|null - fields
   */
  protected function getFields(): ?array
  {
    return $this->fields;
  }

  /**
   * @return DBAdapter
   */
  protected function getDb(): DBAdapter
  {
    return $this->db;
  }

  protected function getFactory() : QueryFactory
  {
    return $this->factory;
  }
}