<?php declare(strict_types=1);

namespace GAR\Exceptions\Checked;

use Exception;

class ChainNotFoundException extends Exception
{
	const MESSAGE = 'Chain was not found';

	public function __construct()
	{
		parent::__construct(self::MESSAGE, 2);
	}


}