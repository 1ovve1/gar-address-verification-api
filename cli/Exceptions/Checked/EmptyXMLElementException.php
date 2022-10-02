<?php declare(strict_types=1);

namespace CLI\Exceptions\Checked;

use Exception;
use JetBrains\PhpStorm\Pure;

class EmptyXMLElementException extends Exception
{
	const MESSAGE = 'Element has no attributes';

	public function __construct()
	{
		parent::__construct(self::MESSAGE);
	}


}