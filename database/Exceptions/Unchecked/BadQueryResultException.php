<?php declare(strict_types=1);

namespace DB\Exceptions\Unchecked;



use DB\Exceptions\ExceptionCodes;
use RuntimeException;
use Throwable;

class BadQueryResultException extends RuntimeException
{
	const MESSAGE_FORMAT = "Bad query request: '%s' " . PHP_EOL . "Message: %s" . PHP_EOL;

	/**
	 * @param string $rawSql
	 * @param Throwable|null $previous
	 */
	public function __construct(string $rawSql, Throwable|null $previous = null)
	{
		if (strlen($rawSql) > 2000) {
			$rawSql = "too large";
		}
		$message = sprintf(self::MESSAGE_FORMAT, $rawSql, $previous?->getMessage() ?? 'PDO return false');

		parent::__construct(
			$message,
			ExceptionCodes::BAD_QUERY_RESULT_CODE,
			$previous
		);
	}

}