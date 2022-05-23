<?php declare(strict_types=1);

namespace GAR\Database\Table;


use Exception;
use GAR\Database\DBAdapter\DBAdapter;
use GAR\Database\DBAdapter\QueryTemplate;
use GAR\Database\Table\SQL\ContinueWhere;
use GAR\Database\Table\SQL\DeleteQuery;
use GAR\Database\Table\SQL\EndQuery;
use GAR\Database\Table\SQL\QueryModel;
use GAR\Database\Table\SQL\SelectQuery;
use GAR\Database\Table\SQL\UpdateQuery;

/**
 * SQL BUILDER CLASS
 *
 * Contains all main sql-operations
 */
class SQLBuilder
  extends
    MetaTable
  implements
    QueryModel, SelectQuery, EndQuery, UpdateQuery, DeleteQuery, ContinueWhere
{
  /**
   * @var string - query string
   */
  private string $query = '';
  /**
   * @var QueryTemplate[] - mapped array, contains named Template statements
   */
  private array $userTemplates = [];
  /**
   * @var QueryTemplate|null - PDO template for insert
   */
  private readonly ?QueryTemplate $insTemple;
  /**
   * @var int - requires values to execute mode
   */
  private int $valuesRequire = 0;
  /**
   * @var array - value stack for execute mode
   */
  private array $valueStack = [];

  /**
   * Create object of query table
   * @param DBAdapter $db
   * @param string $tableName
   * @param int $maxInsStages
   * @param array|null $createOption
   */
  public function __construct(DBAdapter $db,
                              string $tableName,
                              int $maxInsStages,
                              ?array $createOption = null)
  {
    parent::__construct($db, $tableName, $createOption);

    $this->insTemple = $this->getDb()
      ->getInsertTemplate($tableName, $this->getFields(), $maxInsStages);
  }

  /**
   * Insert statement
   *
   * @param $values - array values in format [field => value]
   * @return EndQuery
   */
  function insert(array $values): EndQuery
  {
    $this->reset();

    $this->setVarStack($values);

    $this->setQuery(sprintf(
      "INSERT INTO %s(%s) \nVALUES (%s)\n",
      $this->getTableName(),
      implode(', ', array_keys($values)),
      implode(', ', array_fill(0, count($values), '?')),
    ));
    return $this;
  }

  /**
   * Full-force insert
   *
   * @param array $values - values to insert
   * @return EndQuery
   */
  function forceInsert(array $values): EndQuery
  {
    $this->reset();
    $this->insTemple->exec($values);
    return $this;
  }

  /**
   * Update statement
   *
   * @param string $field - concrete field
   * @param string|int $value - concrete value
   * @return UpdateQuery
   */
  function update(string $field, string|int $value): UpdateQuery
  {
    $this->reset();

    $this->setVarStack($value);

    $this->setQuery(sprintf(
      "UPDATE %s \nSET %s = (%s)\n",
      $this->getTableName(),
      $field,
      '?'
    ));

    return $this;
  }

  /**
   * Delete statement
   *
   * @return DeleteQuery
   */
  function delete(): DeleteQuery
  {
    $this->reset();
    $this->setQuery(sprintf(
      "DELETE FROM %s\n",
      $this->getTableName(),
    ));
    return $this;
  }

  /**
   * Select statement
   *
   * @param array $fields - fields to select in array
   * @param array|null $anotherTables - tables name array (may use [tableName => pseudonym])
   * @return SelectQuery
   */
  function select(array $fields, array $anotherTables = null): SelectQuery
  {
    $this->reset();
    $formattedTables = null;
    if (!is_null($anotherTables)) {
      $formattedTables = $this->implodeWithKeys($anotherTables, ' as ');
    }
    $this->setQuery(sprintf(
      "SELECT %s \nFROM %s\n",
      $this->implodeWithKeys($fields),
      $formattedTables ?? $this->getTableName()
    ));
    return $this;
  }

  /**
   * Find concrete value in field
   *
   * @param string $field - field
   * @param int|string $value - value to find
   * @param string|null $anotherTable - another tables
   * @return array - found value (limit 1) or empty if non
   */
  function findFirst(string $field, int|string $value, ?string $anotherTable = null): array
  {
    $this->reset();

    return $this->select([$field], (is_null($anotherTable)) ? null: [$anotherTable])
      ->where($field, '=', $value)
      ->limit(1)->save();
  }


  /**
   * Where state
   *
   * @param string $field - field to compare
   * @param string $sign - sign to compare
   * @param int|string $value - value to compare
   * @return ContinueWhere
   */
  function where(string $field, string $sign, int|string $value): ContinueWhere
  {
    $this->setVarStack($value);

    $this->setQuery(sprintf(
      "WHERE %s %s (%s) ",
      $field,
      $sign,
      '?'
    ));
    return $this;
  }

  /**
   * Where with and operand
   *
   * @param string $field - field to compare
   * @param string $sign - sign to compare
   * @param int|string $value - value to compare
   * @return ContinueWhere
   */
  function andWhere(string $field, string $sign, int|string $value): ContinueWhere
  {
    $this->setVarStack($value);

    $this->setQuery(sprintf(
      "AND %s %s (%s) ",
      $field,
      $sign,
      '?'
    ));
    return $this;
  }

  /**
   * Where with or operand
   *
   * @param string $field - field to compare
   * @param string $sign - sign to compare
   * @param int|string $value - value to compare
   * @return ContinueWhere
   */
  function orWhere(string $field, string $sign, int|string $value): ContinueWhere
  {
    $this->setVarStack($value);

    $this->setQuery(sprintf(
      "OR %s %s (%s) ",
      $field,
      $sign,
      '?'
    ));

    return $this;
  }

  /**
   * Inner join
   *
   * @param string $table - table name
   * @param array $condition - condition in format [joinField => anotherField]
   * @return SelectQuery
   */
  function innerJoin(string $table, array $condition): SelectQuery
  {
    $this->setQuery(sprintf(
      "INNER JOIN %s ON %s\n",
      $table,
      $this->implodeWithKeys($condition, ' = ', ' AND ')
    ));
    return $this;
  }

  /**
   * Left join
   *
   * @param string $table - table name
   * @param array $condition - condition in format [joinField => anotherField]
   * @return SelectQuery
   */
  function leftJoin(string $table, array $condition): SelectQuery
  {
    $this->setQuery(sprintf(
      "LEFT OUTER JOIN %s ON %s\n",
      $table,
      $this->implodeWithKeys($condition, ' = ', ' AND ')
    ));
    return $this;
  }

  /**
   * Right join to query
   *
   * @param string $table - table name
   * @param array $condition - condition in format [joinField => anotherField]
   * @return SelectQuery
   */
  function rightJoin(string $table, array $condition): SelectQuery
  {
    $this->setQuery(sprintf(
      "RIGHT OUTER JOIN %s ON %s\n",
      $table,
      $this->implodeWithKeys($condition, ' = ', ' AND ')
    ));
    return $this;
  }

  /**
   * Set limit to query
   *
   * @param int $count - limit count
   * @return EndQuery
   */
  function limit(int $count): EndQuery
  {
    $this->setQuery(sprintf(
      "LIMIT %s\n",
      $count
    ));

    return $this;
  }

  /**
   * Execute query chain or forceInsert
   *
   * @return array - result of fetch
   */
  function save(): array
  {
    if (!empty($this->getQuery())) {
      $this->getDb()->prepare($this->query)->execute($this->valueStack);
    } else {
      $this->insTemple->save();
    }

    return $this->getDb()->fetchAll();
  }

  /**
   * Check if name of template exists
   *
   * @param string $checkName - name of template
   * @return bool
   */
  function nameExist(string $checkName): bool
  {
    return array_key_exists($checkName, $this->userTemplates);
  }

  /**
   * Insert new template with curr query
   *
   * @param string $name - name of template
   * @return string
   * @throws Exception
   */
  function name(string $name): string
  {
    if (!$this->nameExist($name)) {
      $this->userTemplates[$name] = $this->getDb()->prepare($this->getQuery())->getTemplate();
    } else {
      throw new Exception('SQLBuilder: name ' . $name . ' already exists');
    }
    return $name;
  }

  /**
   * Execute template
   *
   * @param array $values - values to execute
   * @return array - fetch
   */
  function execute(array $values, ?string $templateName = null): array
  {
    $fetch = [];

    if (is_null($templateName)) {
      $fetch = $this->getDb()->prepare($this->query)->execute($values)->fetchAll();
    } else {
      if ($this->nameExist($templateName)) {
        $fetch = $this->getTemplate($templateName)->exec($values);
      }
    }

    return $fetch;
  }

  /**
   * Reset current query chain
   *
   * @return QueryModel
   */
  function reset(): QueryModel
  {
    $this->setQuery();
    $this->setVarStack();
    return $this;
  }

  /**
   * Set query property
   *
   * @param string|null $query - value to concatenate (null to make empty)
   */
  private function setQuery(?string $query = null): void
  {
    if (is_null($query)) {
      $this->query = '';
    } else {
      $this->query .= $query;
    }
  }

  /**
   * Return query
   *
   * @return string
   */
  public function getQuery(): string
  {
    return $this->query;
  }

  private function getTemplate(string $name) : ?QueryTemplate
  {
    return $this->userTemplates[$name];
  }

  /**
   * Rules for add values in value stack
   *
   * @param array|string|int|null $value
   * @return void
   */
  public function setVarStack(array|string|int|null $value = null): void
  {
    if (is_null($value)) {
      $this->valueStack = [];
      $this->valuesRequire = 0;
    } else if (is_array($value)) {
      $this->valueStack = array_merge($this->valueStack, array_values($value));
      $this->valuesRequire += count($value);
    } else {
      $this->valueStack[] = $value;
      $this->valuesRequire++;
    }
  }


  /**
   * Implode arrays with addition separator
   *
   * @param array $listNames - string names
   * @param string $separator - outside separator
   * @param string $deepSeparator - inside separator
   * @return string - formatted string
   */
  private function implodeWithKeys(array $listNames,
                                   string $separator = '',
                                   string $deepSeparator = ',') : string
  {
    $formatted = [];
    foreach ($listNames as $alterName => $name) {
      if (is_string($alterName)) {
        $formatted[] = $name . $separator . $alterName;
      } else {
        $formatted[] = $name;
      }
    }
    return implode($deepSeparator, $formatted);
  }

  protected function withQuotesIfString(string|int $value) : string {
    return (is_string($value)) ? "'".$value."'": (string)$value;
  }
}