<?php declare(strict_types=1);

namespace GAR\Database\Table;

use GAR\Database\Table\SQL\ClosureWhere;

class SQLClosureBuilder implements ClosureWhere
{
	private string $closureQuery = '';
	private readonly SQLBuilder $refToMainBuilder;
	private bool $startFlag = false;

	/**
	 * @param SQLBuilder $refToMainBuilder
	 */
	public function __construct(SQLBuilder $refToMainBuilder)
	{
		$this->refToMainBuilder = $refToMainBuilder;
	}

	/**
	 *  {@inheritDoc}
	 */
	function where(string|callable $field, string $sign = '', mixed $value = ''): ClosureWhere
	{
		if ($this->startFlag) {
			throw new \RuntimeException("Invalid operation: not use 'where' double times");
		}

		if (is_callable($field)) {
			$closureBuilder = new SQLClosureBuilder($this->refToMainBuilder);

			$field($closureBuilder);

			$this->setClosureQuery(sprintf(
					"(\n\t%s\n)",
					$closureBuilder->getClosureQuery()
				)
			);
		} else {
			$this->refToMainBuilder->setVarStack($value);

			$this->setClosureQuery(
				sprintf(
					"%s %s (%s)",
					$field, $sign, '?'
				)
			);
		}

		$this->startFlag = true;

		return $this;
	}

	/**
	 *  {@inheritDoc}
	 */
	public function andWhere(callable|string $field, string $sign = '', mixed $value = ''): ClosureWhere
	{
		// TODO: Implement andWhere() method.
		if (!$this->startFlag) {
			throw new \RuntimeException("Invalid operation: try make 'andWhere' without 'where'");
		}

		$this->whereBuilder('AND', $field, $sign, $value);

		return  $this;
	}

	/**
	 *  {@inheritDoc}
	 */
	public function orWhere(callable|string $field, string $sign = '', mixed $value = ''): ClosureWhere
	{
		if (!$this->startFlag) {
			throw new \RuntimeException("Invalid operation: try make 'orWhere' without 'where'");
		}

		$this->whereBuilder('OR', $field, $sign, $value);

		return  $this;
	}

	private function whereBuilder(string $operator,
	                              string|callable $field,
	                              string $sign = '',
	                              mixed $value = ''): void
	{
		if (is_callable($field)) {
			$closureBuilder = new SQLClosureBuilder($this->refToMainBuilder);

			$field($closureBuilder);

			$this->setClosureQuery(sprintf(
					" %s (\n\t%s\n)",
					$operator, $closureBuilder->getClosureQuery()
				)
			);
		} else {
			$this->refToMainBuilder->setVarStack($value);

			$this->setClosureQuery(sprintf(
				" %s %s %s (%s)",
				$operator, $field, $sign, '?'
			));
		}
	}

	/**
	 * @return string
	 */
	public function getClosureQuery(): string
	{
		return $this->closureQuery;
	}

	/**
	 * @param string $closureQuery
	 */
	public function setClosureQuery(string $closureQuery): void
	{
		$this->closureQuery .= $closureQuery;
	}
}