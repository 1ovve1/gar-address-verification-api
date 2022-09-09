<?php declare(strict_types=1);

namespace GAR\Exceptions\Unchecked;

use RuntimeException;
use Throwable;

class ServerSideProblemException extends RuntimeException
{
	const MESSAGE = 'Problems with server';

	/**
	 * @param Throwable $previous
	 */
	public function __construct(Throwable $previous)
	{
		parent::__construct($previous->getMessage(), $previous->getCode(), $previous);
	}

}