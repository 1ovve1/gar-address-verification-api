<?php declare(strict_types=1);

namespace DB\Exceptions;

use Exception;

class NullableQueryResultException extends Exception
{
	const MESSAGE = "Nullable query result";

	public function __construct()
	{
		parent::__construct(
			self::MESSAGE,
			ExceptionCodes::NULLABLE_QUERY_RESULT_CODE
		);
	}

}