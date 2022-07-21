<?php

declare(strict_types=1);

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
 *
 * @phpstan-import-type DatabaseContract from \GAR\Database\DBAdapter\DBAdapter
 */
class SQLBuilder implements
    QueryModel,
    SelectQuery,
    EndQuery,
    UpdateQuery,
    DeleteQuery,
    ContinueWhere
{
    /**
     * @var MetaTable|null - Meta table object
     */
    protected readonly ?MetaTable $metaTable;
    /**
     * @var DBAdapter - database
     */
    private readonly DBAdapter $db;
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
     * @var array<DatabaseContract> - value stack for execute mode
     */
    private array $valueStack = [];

    /**
     * Create object of query table
     * @param DBAdapter $db
     * @param MetaTable|null $metaTable
     * @param int $maxInsStages
     */
    public function __construct(
        DBAdapter $db,
        ?MetaTable $metaTable = null,
        int $maxInsStages = 1
    ) {
        $this->db = $db;
        $this->metaTable = $metaTable;

        if (!is_null($metaTable)) {
            $this->insTemple = $this->getDb()
        ->getInsertTemplate(
            $metaTable->getTableName(),
            $metaTable->getFields(),
            $maxInsStages
        );
        }
    }

    /**
     * Create insert template
     *
     * @param  array<string, DatabaseContract> $values - values in field => value fmt
     * @param  string|null $tableName - name of table
     * @return EndQuery
     */
    public function insert(array $values, ?string $tableName = null): EndQuery
    {
        $this->reset();

        $this->setVarStack($values);

        $this->checkTableName($tableName);

        $this->setQuery(sprintf(
            "INSERT INTO %s(%s) \nVALUES (%s)\n",
            $tableName ?? $this->metaTable?->getTableName(),
            implode(', ', array_keys($values)),
            implode(', ', array_fill(0, count($values), '?')),
        ));
        return $this;
    }

    /**
     * Doing forceInsert
     *
     * @param  array<DatabaseContract> $values - values for the force insert
     * @return EndQuery
     */
    public function forceInsert(array $values): EndQuery
    {
        $this->reset();
        if (is_null($this->metaTable) || is_null($this->insTemple)) {
            throw new Exception(
                'SQLBuilder: forceInsert are not supported in this table (check meta table)'
            );
        }
        $this->insTemple->exec($values);
        return $this;
    }

    /**
     * Create update template
     *
     * @param  string $field - field for update
     * @param  DatabaseContract $value - value for upadte
     * @param  string|null $tableName - name of table
     * @return UpdateQuery
     */
    public function update(string $field, mixed $value, ?string $tableName = null): UpdateQuery
    {
        $this->reset();

        $this->setVarStack($value);

        $this->checkTableName($tableName);

        $this->setQuery(sprintf(
            "UPDATE %s \nSET %s = (%s)\n",
            $tableName ?? $this->metaTable?->getTableName(),
            $field,
            '?'
        ));

        return $this;
    }

    /**
     * Creating delete template
     *
     * @param  string|null $tableName - name of table
     * @return DeleteQuery
     */
    public function delete(?string $tableName = null): DeleteQuery
    {
        $this->checkTableName($tableName);

        $this->reset();
        $this->setQuery(sprintf(
            "DELETE FROM %s\n",
            $tableName ?? $this->metaTable?->getTableName(),
        ));
        return $this;
    }

    /**
     * Creating select template
     *
     * @param  array<string> $fields - fields to select
     * @param  array<string>|null $anotherTables - name of another table
     * @return SelectQuery
     */
    public function select(array $fields, array $anotherTables = null): SelectQuery
    {
        $this->reset();

        $this->checkTableName($anotherTables);

        $formattedTables = null;
        if (!is_null($anotherTables)) {
            $formattedTables = $this->implodeWithKeys($anotherTables, ' as ');
        }
        $this->setQuery(sprintf(
            "SELECT %s \nFROM %s\n",
            $this->implodeWithKeys($fields),
            $formattedTables ?? $this->metaTable?->getTableName()
        ));
        return $this;
    }

    /**
     * Finding first element of $field collumn with $value compare
     *
     * @param  string $field - fields name
     * @param  DatabaseContract $value - value for compare
     * @param  string|null $anotherTable - table name
     * @return array<string, mixed>
     */
    public function findFirst(string $field, mixed $value, ?string $anotherTable = null): array
    {
        $this->reset();

        $this->checkTableName($anotherTable);

        return $this->select([$field], (is_null($anotherTable)) ? null : [$anotherTable])
      ->where($field, '=', $value)
      ->limit(1)->save();
    }


    /**
     * Create WHERE template
     *
     * @param  string $field - name of field
     * @param  string $sign - sign for compare
     * @param  DatabaseContract $value - value to compare
     * @return ContinueWhere
     */
    public function where(string $field, string $sign, mixed $value): ContinueWhere
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
     * Create AND WHERE template
     *
     * @param  string $field - name of field
     * @param  string $sign - sign for compare
     * @param  DatabaseContract $value - value to compare
     * @return ContinueWhere
     */
    public function andWhere(string $field, string $sign, mixed $value): ContinueWhere
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
     * Create OR WHERE template
     *
     * @param  string $field - name of field
     * @param  string $sign - sign for compare
     * @param  DatabaseContract $value - value to compare
     * @return ContinueWhere
     */
    public function orWhere(string $field, string $sign, mixed $value): ContinueWhere
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
     * Create INNER JOIN template
     *
     * @param  string $table - name of table
     * @param  array<string, string> $condition - ON condition by fliedName = filedName
     * @return SelectQuery
     */
    public function innerJoin(string $table, array $condition): SelectQuery
    {
        $this->setQuery(sprintf(
            "INNER JOIN %s ON %s\n",
            $table,
            $this->implodeWithKeys($condition, ' = ', ' AND ')
        ));
        return $this;
    }

    /**
     * Create LEFT OUTER JOIN template
     *
     * @param  string $table - name of table
     * @param  array<string, string> $condition - ON condition by fliedName = filedName
     * @return SelectQuery
     */
    public function leftJoin(string $table, array $condition): SelectQuery
    {
        $this->setQuery(sprintf(
            "LEFT OUTER JOIN %s ON %s\n",
            $table,
            $this->implodeWithKeys($condition, ' = ', ' AND ')
        ));
        return $this;
    }

    /**
     * Create RIGHT OUTER JOIN template
     *
     * @param  string $table - name of table
     * @param  array<string, string> $condition - ON condition by fliedName = filedName
     * @return SelectQuery
     */
    public function rightJoin(string $table, array $condition): SelectQuery
    {
        $this->setQuery(sprintf(
            "RIGHT OUTER JOIN %s ON %s\n",
            $table,
            $this->implodeWithKeys($condition, ' = ', ' AND ')
        ));
        return $this;
    }

    /**
     * Create LIMIT $count template
     * @param  positive-int $count - limit count
     * @return EndQuery
     */
    public function limit(int $count): EndQuery
    {
        $this->setQuery(sprintf(
            "LIMIT %s\n",
            $count
        ));

        return $this;
    }

    /**
     * Creating ORDER BY template
     *
     * @param  string $field - name of field
     * @param  bool|boolean $asc - type of sort
     * @return EndQuery
     */
    public function orderBy(string $field, bool $asc = true): endQuery
    {
        $this->setQuery(sprintf(
            "ORDER BY %s %s\n",
            $field,
            ($asc) ? 'ASC' : 'DESC'
        ));

        return $this;
    }


    /**
     * Save and execute query
     *
     * @return array<mixed>
     */
    public function save(): array
    {
        if (!empty($this->getQuery())) {
            $this->getDb()->prepare($this->query)->execute($this->valueStack);
        } elseif (!is_null($this->metaTable) && !is_null($this->insTemple)) {
            $this->insTemple->save();
        }

        return $this->getDb()->fetchAll(DBAdapter::PDO_F_ALL);
    }

    /**
     * Check if template with name $checkName exists
     * @param  string $checkName - name of template
     * @return bool
     */
    public function nameExist(string $checkName): bool
    {
        return array_key_exists($checkName, $this->userTemplates);
    }

    /**
     * Create template with name $name
     *
     * @param  string $name - name of template
     * @return void
     */
    public function name(string $name): void
    {
        if (!$this->nameExist($name)) {
            $this->userTemplates[$name] = $this->getDb()->prepare($this->getQuery())->getTemplate();
        } else {
            throw new Exception('SQLBuilder: name ' . $name . ' already exists');
        }
    }

    /**
     * Execute template with name $templateName by $values
     * @param  array<DatabaseContract> $values - values to execute
     * @param  string|null $templateName - name of template
     * @return array<mixed>
     */
    public function execute(array $values, ?string $templateName = null): array
    {
        $fetch = [];

        if (is_null($templateName)) {
            $fetch = $this->getDb()->prepare($this->query)->execute($values)->fetchAll(DBAdapter::PDO_F_ALL);
        } else {
            if ($this->nameExist($templateName)) {
                $fetch = $this->getTemplate($templateName)->exec($values);
            }
        }

        return $fetch;
    }

    /**
     * Reset query buffer
     * @return QueryModel
     */
    public function reset(): QueryModel
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

    /**
     * Return curr database
     *
     * @return DBAdapter - database adapter
     */
    protected function getDb(): DBAdapter
    {
        return $this->db;
    }

    /**
     * Return user template by name
     *
     * @param string $name
     * @return QueryTemplate
     * @throws \RuntimeException
     */
    private function getTemplate(string $name): QueryTemplate
    {
        if (empty($this->userTemplates)) {
            throw new \RuntimeException('SQLBuilder error: call to empty tempate');
        }
        return $this->userTemplates[$name];
    }

    /**
     * Rules for add values in value stack
     *
     * @param DatabaseContract|array<DatabaseContract>|null $value
     * @return void
     */
    public function setVarStack(mixed $value = null): void
    {
        if (is_null($value)) {
            $this->valueStack = [];
            $this->valuesRequire = 0;
        } elseif (is_array($value)) {
            $this->valueStack = array_merge($this->valueStack, array_values($value));
            $this->valuesRequire += count($value);
        } else {
            $this->valueStack[] = $value;
            $this->valuesRequire++;
        }
    }

    /**
     * Check if table name are exist and return create exception if non
     *
     * @param string|array<string>|null $tableName - name of table
     * @return void
     * @throws Exception - if tableName not exists
     */
    public function checkTableName(string|array|null $tableName): void
    {
        if (is_null($tableName) && is_null($this->metaTable)) {
            throw new Exception('SQLBuilder exception: require table name');
        }
    }

    /**
     * Implode arrays with addition separator
     *
     * @param array<string> $listNames - string names
     * @param string $separator - outside separator
     * @param string $deepSeparator - inside separator
     * @return string - formatted string
     */
    private function implodeWithKeys(
        array $listNames,
        string $separator = '',
        string $deepSeparator = ','
    ): string {
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
}
