<?php declare(strict_types=1);

namespace Tests\Mock;

use QueryBox\QueryBuilder\ActiveRecord\ActiveRecordImpl;
use QueryBox\QueryBuilder\ActiveRecord\QueryBox;

class FakeActiveRecordImpl extends ActiveRecordImpl
{
	function __construct(string $template = '')
	{
		parent::__construct(new QueryBox($template));
	}
}