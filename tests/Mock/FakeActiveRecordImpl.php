<?php declare(strict_types=1);

namespace Tests\Mock;

use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecordImpl;
use DB\ORM\QueryBuilder\ActiveRecord\QueryBox;

class FakeActiveRecordImpl extends ActiveRecordImpl
{
	function __construct(string $template = '')
	{
		parent::__construct(new QueryBox($template));
	}
}