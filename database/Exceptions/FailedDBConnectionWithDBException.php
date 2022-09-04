<?php declare(strict_types=1);

namespace DB\Exceptions;

use Exception;

class FailedDBConnectionWithDBException extends Exception
{
	const MESSAGE_TEMPLATE = "Failed connection to database with these params: " . PHP_EOL .
							 "Dsn: %s" . PHP_EOL . "Message from DBAdapter: %s";

	/**
	 * @param string $dsn
	 * @param Exception $previous
	 */
	public function __construct(string $dsn, Exception $previous)
	{
		$message = sprintf(self::MESSAGE_TEMPLATE, $dsn, $previous->getMessage());
		parent::__construct(
			$message,
			ExceptionCodes::FAILED_DB_CONNECTION_VIA_DSN_CODE,
			$previous
		);
	}

}