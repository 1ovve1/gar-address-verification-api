<?php

declare(strict_types=1);

namespace DB\ORM\DBAdapter\PDO;

use DB\Exceptions\BadQueryResultException;
use DB\ORM\DBAdapter\{DBAdapter, QueryResult, QueryTemplate};
use PDOException;
use PDOStatement;

/**
 * Simple PDOTemplate container
 *
 * @phpstan-import-type DatabaseContract from DBAdapter
 */
class PDOTemplate implements QueryTemplate
{
    /**
     * @var PDOStatement - prepared stage object
     */
    protected readonly PDOStatement $template;

    /**
     * @param PDOStatement $template - prepared statement
     */
    public function __construct(PDOStatement $template)
    {
        $this->template = $template;
    }

	/**
	 * {@inheritDoc}
	 */
    public function exec(array $values = []): QueryResult
    {
        try {
            $res = $this->template->execute($values);
        } catch (PDOException $pdoException) {
            throw new BadQueryResultException($this->template->queryString, $pdoException);
        }
        if ($res === false) {
	        throw new BadQueryResultException($this->template->queryString);
        }
        return new PDOQueryResult($this->template);
    }

	/**
	 * {@inheritDoc}
	 */
    public function save(): QueryResult
    {
		return $this->exec();
    }
}
