<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\Utils;

use RuntimeException;

class SQLNestedWhereConstructor
{
	private array $buffer = [];
	private string $query = '';

	/**
	 * @param array $refToBuffer
	 */
	public function __construct()
	{}

	/**
	 * {@inheritDoc}
	 */
	public function where(callable|string $field_or_nested_clbk,
	                      mixed $sign_or_value = null,
	                      mixed $value = null): self
	{
		$query = self::genConditionByArguments(
			$field_or_nested_clbk,
			$sign_or_value,
			$value
		);

		$this->setQuery($query);
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function andWhere(callable|string $field_or_nested_clbk,
	                         mixed $sign_or_value = null,
	                         mixed $value = null): self
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
	                        mixed $value = null): self
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
	protected function genConditionByArguments(callable|string $field_or_nested_clbk,
                                             mixed $sign_or_value = null,
                                             mixed $value = null) : string
	{
		$query = '';

		if (is_callable($callback = $field_or_nested_clbk)) {
			$query = self::makeConditionByNestedCallback($callback);

		} else if (null === $value) {
			$field = $field_or_nested_clbk;
			$value = $sign_or_value;
			$this->setBuffer($value);

			$query = self::makeConditionByEquals($field);

		} else {
			$field = $field_or_nested_clbk;
			$sign = $sign_or_value;
			$this->setBuffer($value);

			$query = self::makeConditionBySign($field, $sign);

		}

		return $query;
	}

	/**
	 * @param callable $nestedCallback
	 * @return void
	 */
	protected static function makeConditionByNestedCallback(callable $nestedCallback): void
	{
		$nestedBuilder = new self();
		$nestedCallback($nestedBuilder);

		$this->setQuery(' (' . $nestedBuilder->getQuery() . ')');
		$this->setBuffer($nestedBuilder->getBuffer());
	}

	/**
	 * @param string $field
	 * @return string
	 */
	protected static function makeConditionByEquals(string $field): string
	{
		return $field . ' = (?)';
	}

	/**
	 * @param string $field
	 * @param mixed $sign
	 * @return string
	 */
	protected static function makeConditionBySign(string $field, mixed $sign): string
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
	protected function setQuery(string $query): void
	{
		$this->query .= $query;
	}

	public function getBuffer(): array
	{
		return $this->buffer;
	}

	protected function setBuffer(mixed $values): void
	{
		if (!is_array($values)) {
			$values = [$values];
		}
		$this->buffer = array_merge($this->buffer, $values);
	}
}