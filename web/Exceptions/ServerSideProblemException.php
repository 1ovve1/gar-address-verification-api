<?php declare(strict_types=1);

namespace GAR\Exceptions;

use Exception;
use Throwable;

class ServerSideProblemException extends Exception
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