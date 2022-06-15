<?php

namespace GAR\Database\Table;


use GAR\Database\DBAdapter\DBAdapter;
use GAR\Database\Table\Container\QueryFactory;
use GAR\Database\Table\Container\QueryGenerator;

/**
 * Meta table object, that doing all manipulation like creating table, get meta data and other
 *
 */
class MetaTable
{
  /**
   * @var DBAdapter $db - database object
   */
  private readonly DBAdapter $db;
  /**
   * @var string $tableName - name of table
   */
  private readonly string $tableName;
  /**
   * @var array<mixed> $fields - table fields
   */
  private array $fields;
  /**
   * @var array<mixed> $metaInfo - full information about table
   */
  private readonly array $metaInfo;
  /**
   * @var QueryFactory $factory - factory of sql queries
   */
  private readonly QueryFactory $factory;

  /**
   * @param DBAdapter $db - database adapter connection
   * @param string $tableName - name of table
   * @param array<string, string>|null $createOption - option for create table
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

    $meta = $this->getMetaInfoAndFields($tableName);
    $this->metaInfo = $meta['metaInfo'];
    $this->fields = $meta['fields'];
  }

  /**
   * Getting meta info from table meta (only for mysql)
   * 
   * @param string $tableName - name of table
   * @return array{metaInfo: array<mixed>, fields: array<mixed>}
   */
  private function getMetaInfoAndFields(string $tableName) : array
  {
    $query = $this->getFactory()->genMetaQuery($tableName);

    $metaInfo = $this->getDb()->rawQuery($query)->fetchAll($this->getDb()::PDO_F_ALL);
    $tableFields = $this->getDb()->rawQuery($query)->fetchAll($this->getDb()::PDO_F_COL);

    foreach ($metaInfo as $field) {
      if (!empty($field['Extra'])) {
        $tableFields = array_diff($tableFields, [$field['Field']]);
      }
    }
    return ['metaInfo' => $metaInfo, 'fields' => $tableFields];
  }

  /**
   * Create table using curr connected, name of table and fields
   * 
   * @param string $tableName - name of table
   * @param array<string, string> $fieldsToCreate - fields and their params
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
   * 
   * @param string $tableName - name of table
   * @return bool
   * @throws \RuntimeException
   */
  protected function tableExistsAndDropCheck(string $tableName) : bool
  {
    $connection = $this->getDb();
    $tableList = $connection
      ->rawQuery($this->getFactory()->genShowTableQuery())
      ->fetchAll($this->getDb()::PDO_F_COL);

    if (!is_array($tableList)) {
      throw new \RuntimeException('MetaTable error: $tableList should return array, ' . gettype($tableList) . " given");
    }
    
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
   * 
   * @return array<mixed> - meta info about table
   */
  public function getMetaInfo(): array
  {
    return $this->metaInfo;
  }

  /**
   * Return fields for curr table
   * @return array<mixed> - fields
   */
  public function getFields(): array
  {
    return $this->fields;
  }

  /**
   * Return database connection
   * @return DBAdapter
   */
  protected function getDb(): DBAdapter
  {
    return $this->db;
  }

  /**
   * Return SQLGenerator
   * @return QueryFactory
   */
  protected function getFactory() : QueryFactory
  {
    return $this->factory;
  }
}