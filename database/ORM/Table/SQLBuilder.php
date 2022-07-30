<?php

declare(strict_types=1);

namespace DB\ORM\Table;

use Exception;
use DB\ORM\DBAdapter\{
	DBAdapter, QueryTemplate
};
use DB\ORM\Table\SQL\{
	ContinueWhere, DeleteQuery, EndQuery, QueryModel, SelectQuery, UpdateQuery
};

/**
 * SQL BUILDER CLASS
 *
 * Contains all main sql-operations
 *
 * @phpstan-import-type DatabaseContract from \DB\ORM\DBAdapter\DBAdapter
 */
class SQLBuilder implements
    QueryModel,
    SelectQuery,
    EndQuery,
    UpdateQuery,
    DeleteQuery,
    ContinueWhere
{
    /** @var MetaTable|null - Meta table object */
    protected readonly ?MetaTable $metaTable;

	/** @var DBAdapter - database */
    private readonly DBAdapter $db;

	/** @var string - query string */
    private string $query = '';

	/** @var QueryTemplate[] - mapped array, contains named Template statements */
    private array $userTemplates = [];

	/** @var QueryTemplate|null - PDO template for insert */
    private readonly ?QueryTemplate $insTemple;

	/** @var int - requires values to execute mode */
    private int $valuesRequire = 0;

	/** @var array<DatabaseContract> - value stack for execute mode */
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

        if (null !== $metaTable) {
            $this->insTemple = $this->getDb()
								    ->getInsertTemplate(
								        $metaTable->getTableName(),
								        $metaTable->getFields(),
								        $maxInsStages
								    );
        }
    }

	/**
	 * {@inheritDoc}
	 */
    public function insert(array $values, ?string $tableName = null): EndQuery
    {
        $this->reset();

        $this->setVarStack($values);

        $this->checkTableName($tableName);

        $this->setQuery(sprintf(
            'INSERT INTO %s(%s) VALUES (%s)',
            $tableName ?? $this->metaTable?->getTableName(),
            implode(', ', array_keys($values)),
            implode(', ', array_fill(0, count($values), '?')),
        ));
        return $this;
    }

	/**
	 * {@inheritDoc}
	 */
    public function forceInsert(array $values): EndQuery
    {
        $this->reset();
        if (null === $this->metaTable || null === $this->insTemple) {
            throw new Exception(
                'SQLBuilder: forceInsert are not supported in this table (check meta table)'
            );
        }
        $this->insTemple->exec($values);
        return $this;
    }

	/**
	 * {@inheritDoc}
	 */
    public function update(string $field, mixed $value, ?string $tableName = null): UpdateQuery
    {
        $this->reset();

        $this->setVarStack($value);

        $this->checkTableName($tableName);

        $this->setQuery(sprintf(
            'UPDATE %s SET %s = (%s)',
            $tableName ?? $this->metaTable?->getTableName(),
            $field,
            '?'
        ));

        return $this;
    }

	/**
	 * {@inheritDoc}
	 */
    public function delete(?string $tableName = null): DeleteQuery
    {
        $this->checkTableName($tableName);

        $this->reset();
        $this->setQuery(sprintf(
            'DELETE FROM %s',
            $tableName ?? $this->metaTable?->getTableName(),
        ));
        return $this;
    }

	/**
	 * {@inheritDoc}
	 */
    public function select(array $fields, array $anotherTables = null): SelectQuery
    {
        $this->reset();

        $this->checkTableName($anotherTables);

        $formattedTables = null;
        if (null !== $anotherTables) {
            $formattedTables = $this->implodeWithKeys($anotherTables, ' as ');
        }
        $this->setQuery(sprintf(
            'SELECT %s FROM %s',
            $this->implodeWithKeys($fields),
            $formattedTables ?? $this->metaTable?->getTableName()
        ));
        return $this;
    }

	/**
	 * {@inheritDoc}
	 */
    public function findFirst(string $field, mixed $value, ?string $anotherTable = null): array
    {
        $this->reset();

        $this->checkTableName($anotherTable);

        return $this->select([$field], (null === $anotherTable) ? null : [$anotherTable])
      ->where($field, '=', $value)
      ->limit(1)->save();
    }


	/**
	 * {@inheritDoc}
	 */
	public function where(callable|string $field_or_nested_clbk,
	                      mixed $sign_or_value = null,
	                      mixed $value = null): ContinueWhere
    {
		$whereBuilder = new SQLWhereBuilder($this);

		$whereBuilder->where($field_or_nested_clbk, $sign_or_value, $value);
		$this->setQuery(' WHERE ' . $whereBuilder->getQuery());

        return $this;
    }

	/**
	 * {@inheritDoc}
	 */
	public function andWhere(callable|string $field_or_nested_clbk,
	                      mixed $sign_or_value = null,
	                      mixed $value = null): ContinueWhere
    {
	    $whereBuilder = new SQLWhereBuilder($this);

	    $whereBuilder->andWhere($field_or_nested_clbk, $sign_or_value, $value);
	    $this->setQuery($whereBuilder->getQuery());

        return $this;
    }

	/**
	 * {@inheritDoc}
	 */
	public function orWhere(callable|string $field_or_nested_clbk,
	                      mixed $sign_or_value = null,
	                      mixed $value = null): ContinueWhere
    {
	    $whereBuilder = new SQLWhereBuilder($this);

	    $whereBuilder->orWhere($field_or_nested_clbk, $sign_or_value, $value);
	    $this->setQuery($whereBuilder->getQuery());

	    return $this;
    }

	/**
	 * {@inheritDoc}
	 */
    public function innerJoin(string $table, array $condition): SelectQuery
    {
        $this->setQuery(sprintf(
            ' INNER JOIN %s ON %s',
            $table,
            $this->implodeWithKeys($condition, ' = ', ' AND ')
        ));
        return $this;
    }

	/**
	 * {@inheritDoc}
	 */
    public function leftJoin(string $table, array $condition): SelectQuery
    {
        $this->setQuery(sprintf(
            ' LEFT OUTER JOIN %s ON %s',
            $table,
            $this->implodeWithKeys($condition, ' = ', ' AND ')
        ));
        return $this;
    }

	/**
	 * {@inheritDoc}
	 */
    public function rightJoin(string $table, array $condition): SelectQuery
    {
        $this->setQuery(sprintf(
            ' RIGHT OUTER JOIN %s ON %s',
            $table,
            $this->implodeWithKeys($condition, ' = ', ' AND ')
        ));
        return $this;
    }

	/**
	 * {@inheritDoc}
	 */
    public function limit(int $count): EndQuery
    {
        $this->setQuery(sprintf(
            " LIMIT %s",
            $count
        ));

        return $this;
    }

	/**
	 * {@inheritDoc}
	 */
    public function orderBy(string $field, bool $asc = true): endQuery
    {
        $this->setQuery(sprintf(
            " ORDER BY %s %s",
            $field,
            ($asc) ? 'ASC' : 'DESC'
        ));

        return $this;
    }


	/**
	 * {@inheritDoc}
	 */
    public function save(): array
    {
        if (!empty($this->getQuery())) {
            $this->getDb()->prepare($this->query)->execute($this->valueStack);
        } elseif (null !== $this->metaTable && null !== $this->insTemple) {
            $this->insTemple->save();
        }

        return $this->getDb()->fetchAll(DBAdapter::PDO_F_ALL);
    }

	/**
	 * {@inheritDoc}
	 */
    public function nameExist(string $checkName): bool
    {
        return array_key_exists($checkName, $this->userTemplates);
    }

	/**
	 * {@inheritDoc}
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
	 * {@inheritDoc}
	 */
    public function execute(array $values, ?string $templateName = null): array
    {
        $fetch = [];

        if (null === $templateName) {
            $fetch = $this->getDb()->prepare($this->query)->execute($values)->fetchAll(DBAdapter::PDO_F_ALL);
        } else {
            if ($this->nameExist($templateName)) {
                $fetch = $this->getTemplate($templateName)->exec($values);
            }
        }

        return $fetch;
    }

    /**
     * {@inheritDoc}
     */
    public function reset(): QueryModel
    {
        $this->resetQuery();
        $this->resetVarStack();
        return $this;
    }

    /**
     * Set query property
     *
     * @param string $query - value to concatenate (null to make empty)
     */
    private function setQuery(string $query): void
    {
        $this->query .= $query;
    }

	private function resetQuery(): void
	{
		$this->query = '';
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
     * @param DatabaseContract|array<DatabaseContract> $value
     * @return void
     */
    public function setVarStack(mixed $value): void
    {
        if (is_array($value)) {
            $this->valueStack = array_merge($this->valueStack, array_values($value));
            $this->valuesRequire += count($value);
        } else {
            $this->valueStack[] = $value;
            $this->valuesRequire++;
        }
    }

	private function resetVarStack(): void
	{
		$this->valueStack = [];
		$this->valuesRequire = 0;
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
        if (null === $tableName && null === $this->metaTable) {
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
        string $deepSeparator = ', '
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
