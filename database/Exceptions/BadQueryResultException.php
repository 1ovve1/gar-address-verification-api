<?php declare(strict_types=1);

namespace DB\Exceptions;



use Exception;

class BadQueryResultException extends Exception
{
	const MESSAGE_FORMAT = "Bad query request: '%s' " . PHP_EOL . "Message: %s" . PHP_EOL;

	/**
	 * @param string $rawSql
	 * @param Exception|null $previous
	 */
	public function __construct(string $rawSql, Exception|null $previous = null)
	{
		$message = sprintf(self::MESSAGE_FORMAT, $rawSql, $previous?->getMessage() ?? 'PDO return false');

		parent::__construct(
			$message,
			ExceptionCodes::BAD_QUERY_RESULT_CODE,
			$previous
		);
	}

}