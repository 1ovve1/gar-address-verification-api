<?php

declare(strict_types=1);

namespace DB\ORM\DBAdapter\PDO;

use DB\ORM\DBAdapter\{
	DBAdapter, QueryTemplate
};

/**
 * Simple PDOTemplate container
 *
 * @phpstan-import-type DatabaseContract from DBAdapter
 */
class PDOTemplate implements QueryTemplate
{
    /**
     * @var \PDOStatement - prepared stage object
     */
    protected readonly \PDOStatement $template;

    /**
     * @param \PDOStatement $template - prepared statement
     */
    public function __construct(\PDOStatement $template)
    {
        $this->template = $template;
    }

    /**
     * Execute template and return result
     *
     * @param  array<DatabaseContract> $values - values to execute
     * @return array<mixed>
     * @throws \RuntimeException
     */
    public function exec(array $values): array
    {
        try {
            $res = $this->template->execute($values);
        } catch (\PDOException $e) {
            throw new \RuntimeException($e->getMessage());
        }
        if ($res === false) {
            throw new \RuntimeException('PDOTemplate (QueryTemplate) error: bad execute');
        }
        return $this->template->fetchAll(DBAdapter::PDO_F_ALL);
    }

    /**
     * Ignored in this implementation
     *
     * @return self
     */
    public function save(): self
    {
        return $this;
    }
}
