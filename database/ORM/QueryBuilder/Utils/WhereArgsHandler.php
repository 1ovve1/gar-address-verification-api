<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\Utils;

use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\Templates\Conditions;

trait WhereArgsHandler
{
	protected static function handleWhereArgs(string $field,
	                                          int|float|bool|string $sign_or_value = '',
	                                          float|int|bool|string|null $value = null) : array
	{

		// now we try to make our 'where' by different params
		if (null === $value) {
			$sign = Conditions::EQ->value;
			$value = $sign_or_value;

		} else if(Conditions::tryFind($sign_or_value)) {
			$sign = Conditions::tryFind($sign_or_value);

		} else {
			DBFacade::dumpException(null, 'Incorrect params', func_get_args());
		}

		return [$field, $sign, $value];
	}
}