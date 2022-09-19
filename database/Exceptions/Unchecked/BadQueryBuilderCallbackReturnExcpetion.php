<?php declare(strict_types=1);

namespace DB\Exceptions\Unchecked;

use JetBrains\PhpStorm\Pure;
use RuntimeException;

class BadQueryBuilderCallbackReturnExcpetion extends RuntimeException
{
	const MESSAGE_TEMPLATE = "Callback should return ActiveRecord instance, but actual return '%s'";

	/**
	 * @param mixed $object
	 */
	public function __construct($object)
	{
		parent::__construct(sprintf(
			self::MESSAGE_TEMPLATE,
			print_r($object, true)
		));
	}


}