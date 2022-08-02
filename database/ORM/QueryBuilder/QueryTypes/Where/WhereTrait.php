<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Where;

use DB\ORM\DBFacade;

trait WhereTrait
{
	/**
	 * {@inheritDoc}
	 */
	public function where(callable|array|string $field_or_nested_clbk,
	                      int|float|bool|string|null $sign_or_value = '',
	                      float|int|bool|string|null $value = null): WhereQuery
	{
		// if it first arg are callback then we use nested where
		if (is_callable($callback = $field_or_nested_clbk)) {
			return new ImplNestedWhere($this, $callback);
		}

		[$field, $sign, $value] = DBFacade::whereArgsHandler($field_or_nested_clbk, $sign_or_value, $value);

		return new ImplWhere($this, $field, $sign, $value);

	}
}