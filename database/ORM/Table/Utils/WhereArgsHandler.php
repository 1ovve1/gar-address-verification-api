<?php declare(strict_types=1);

namespace DB\ORM\Table\Utils;

use DB\ORM\DBFacade;
use DB\ORM\Table\Templates\Conditions;

trait WhereArgsHandler
{
	private function handleWhereArgs(string $field,
                                     string $sign_or_value = '',
                                     float|int|bool|string|null $value = null) : array
	{

		// now we try to make our 'where' by different params
		if (null === $value) {
			$sign = Conditions::EQ->value;
			$value = $sign_or_value;

		} else if(Conditions::tryFind($sign_or_value)) {
			$sign = Conditions::tryFind($sign_or_value);

		} else {
			DBFacade::dumpException($this, 'Incorrect params', func_get_args());
		}

		return [$field, $sign, $value];
	}
}