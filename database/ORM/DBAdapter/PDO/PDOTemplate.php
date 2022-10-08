<?php

declare(strict_types=1);

namespace DB\ORM\DBAdapter\PDO;

use DB\Exceptions\Unchecked\BadQueryResultException;
use DB\ORM\DBAdapter\{QueryResult, QueryTemplateBindAble};
use PDOException;
use PDOStatement;

/**
 * Simple PDOTemplate container
 */
class PDOTemplate implements QueryTemplateBindAble
{
    /**
     * @var PDOStatement - prepared stage object
     */
    public readonly PDOStatement $template;

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
    public function exec(?array $values = null): QueryResult
    {
        try {
            $res = $this->template->execute($values);
        } catch (PDOException $pdoException) {
            throw new BadQueryResultException($this->template->queryString, $pdoException);
        } finally {
			if (filter_var($_ENV['LOG_QUERY_RESULTS'], FILTER_VALIDATE_BOOL)) {
				$this->logLastQueryExecute();
			}
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

	/**
	 * @inheritDoc
	 */
	public function bindParams(array &$params = [], bool $columnMod = false): QueryTemplateBindAble
	{
		if (false === $columnMod) {
			$this->bindParamsManual($params);
		} else {
			$this->bindParamsByColumn($params);
		}

		return $this;
	}

	/**
	 * @param array<DatabaseContract> $params
	 * @return void
	 */
	private function bindParamsManual(array &$params): void
	{
		$keyFlip = array_flip(array_keys($params));

		foreach ($params as $key => &$value) {
			$index = $keyFlip[$key];

			$this->template->bindParam($index + 1, $value);
		}
	}

	/**
	 * @param array<string, array<DatabaseContract>> $params
	 * @return void
	 */
	private function bindParamsByColumn(array &$params): void
	{
		$columnsFlip = array_flip(array_keys($params));
		$columnsCount = count($params);

		foreach ($params as $column => &$columnValue) {
			$columnIndex = $columnsFlip[$column];

			foreach ($columnValue as $rowIndex => &$rowValue) {
				$bindRes = $this->template->bindParam($columnIndex + ($rowIndex * $columnsCount) + 1, $rowValue);

				if (false === $bindRes) {
					throw new BadQueryResultException("bad try to allocate param by buffer[{$columnIndex}][{$rowIndex}] reference");
				}
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function bindValues(array $values = []): QueryTemplateBindAble
	{
		foreach ($values as $index => $value) {
			$this->template->bindValue($index + 1, $value);
		}

		return $this;
	}

	public function logLastQueryExecute(): void
	{
		ob_start();
		$this->template->debugDumpParams();
		$r = ob_get_contents();
		ob_end_clean();

		if (false !== $r) {
			/** @var \Monolog\Logger $logger */
			$logger = $_SERVER['MONOLOG']();
			$logger->notice(PHP_EOL . $r);
		}
	}

}
