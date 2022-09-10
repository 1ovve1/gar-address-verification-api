<?php declare(strict_types=1);

namespace DB\Exceptions\Unchecked;

use DB\Exceptions\ExceptionCodes;
use RuntimeException;

class OperationNotFoundException extends RuntimeException
{
	const MESSAGE_TEMPLATE = "Condition '%s' was not found";

	/**
	 * @param string $operation - name of operation
	 */
	public function __construct(string $operation)
	{
		$message = sprintf(self::MESSAGE_TEMPLATE, $operation);

		parent::__construct($message, ExceptionCodes::OPERATION_WAS_NOT_FOUND);
	}


}