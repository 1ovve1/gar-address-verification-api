<?php declare(strict_types=1);

namespace DB\ORM\Table;

use DB\ORM\Table\SQL\Conditions;
use DB\ORM\Table\SQL\WhereQuery;
use DB\ORM\Table\SQL\NestedWhereQuery;
use RuntimeException;

class SQLWhereBuilder implements NestedWhereQuery
{
	private SQLBuilder $refBuilder;
	private string $query = '';

	/**
	 * @param SQLBuilder $refBuilder
	 */
	public function __construct(SQLBuilder $refBuilder)
	{
		$this->refBuilder = $refBuilder;
	}

	/**
	 * {@inheritDoc}
	 */
	public function where(callable|string $field_or_nested_clbk,
	                      mixed $sign_or_value = null,
	                      mixed $value = null): NestedWhereQuery
	{
		$query = self::genConditionByArguments(
			$field_or_nested_clbk,
			$sign_or_value,
			$value
		);

		$this->setQuery(' ' . $query);
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function andWhere(callable|string $field_or_nested_clbk,
	                         mixed $sign_or_value = null,
	                         mixed $value = null): NestedWhereQuery
	{
		$query = self::genConditionByArguments(
			$field_or_nested_clbk,
			$sign_or_value,
			$value
		);

		$this->setQuery(' AND ' . $query);
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function orWhere(callable|string $field_or_nested_clbk,
	                        mixed $sign_or_value = null,
	                        mixed $value = null): NestedWhereQuery
	{
		$query = self::genConditionByArguments(
			$field_or_nested_clbk,
			$sign_or_value,
			$value
		);

		$this->setQuery(' OR ' . $query);
		return $this;
	}

	/**
	 * Take basic WHERE arguments and prepare them for any scenarios
	 *
	 * @param callable|string $field_or_nested_clbk
	 * @param mixed|null $sign_or_value
	 * @param mixed|null $value
	 * @return string
	 */
	private function genConditionByArguments(callable|string $field_or_nested_clbk,
                                             mixed $sign_or_value = null,
                                             mixed $value = null) : string
	{
		$query = '';

		if (is_callable($callback = $field_or_nested_clbk)) {
			$query = self::makeConditionByNestedCallback($callback, $this->refBuilder);

		} else if (null === $value) {
			$field = $field_or_nested_clbk;
			$value = $sign_or_value;
			$this->refBuilder->setVarStack($value);

			$query = self::makeConditionByEquals($field);

		} else {
			$field = $field_or_nested_clbk;
			$sign = $sign_or_value;
			$this->refBuilder->setVarStack($value);

			$query = self::makeConditionBySign($field, $sign);

		}

		return $query;
	}

	/**
	 * @param callable $nestedCallback
	 * @param SQLBuilder $refOnBuilder
	 * @return string
	 */
	private static function makeConditionByNestedCallback(callable $nestedCallback,
	                                                      SQLBuilder $refOnBuilder): string
	{
		$nestedBuilder = new SQLWhereBuilder($refOnBuilder);
		$nestedCallback($nestedBuilder);

		return '(' . $nestedBuilder->getQuery() . ')';
	}

	/**
	 * @param string $field
	 * @return string
	 */
	private static function makeConditionByEquals(string $field): string
	{
		return $field . ' = (?)';
	}

	/**
	 * @param string $field
	 * @param mixed $sign
	 * @return string
	 */
	private static function makeConditionBySign(string $field, mixed $sign): string
	{
		if (!Conditions::tryFind($sign)) {
			throw new RuntimeException("Unknown or unsupported sign '{$sign}'");
		}

		return $field . ' ' . $sign . ' (?)';
	}

	/**
	 * @return string
	 */
	public function getQuery(): string
	{
		return $this->query;
	}

	/**
	 * @param string $query
	 */
	public function setQuery(string $query): void
	{
		$this->query .= $query;
	}
}