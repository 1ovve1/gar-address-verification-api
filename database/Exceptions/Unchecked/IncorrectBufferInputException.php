<?php declare(strict_types=1);

namespace DB\Exceptions\Unchecked;


use DB\Exceptions\ExceptionCodes;
use RuntimeException;

class IncorrectBufferInputException extends RuntimeException
{
	const MESSAGE_TEMPLATE = "Curr buffer configuration (require '%d' rows) are not compilable with actual given values (['%s'])";

	/**
	 * @param int $requireLengthOfInputArray
	 * @param array<mixed> $givenValues
	 */
	public function __construct(int $requireLengthOfInputArray, array $givenValues)
	{
		$message = sprintf(self::MESSAGE_TEMPLATE, $requireLengthOfInputArray, implode("', '", $givenValues));
		parent::__construct(
			$message,
			ExceptionCodes::INCORRECT_BUFFER_INPUT_EXCEPTION_CODE
		);
	}

}