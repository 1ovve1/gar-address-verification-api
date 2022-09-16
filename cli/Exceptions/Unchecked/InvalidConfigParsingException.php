<?php declare(strict_types=1);

namespace CLI\Exceptions\Unchecked;

use RuntimeException;

class InvalidConfigParsingException extends RuntimeException
{
	const MESSAGE = 'Bad config parse';

	/**
	 * @param string $message
	 */
	public function __construct(string $message)
	{
		parent::__construct(
			sprintf("Problem: %s" . PHP_EOL ."Message: %s", self::MESSAGE, $message)
		);
	}


}