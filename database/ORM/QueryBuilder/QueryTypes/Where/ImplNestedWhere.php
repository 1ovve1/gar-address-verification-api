<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Where;

use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\QueryTypes\NestedCondition\ClientNestedCondition;
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;
use DB\ORM\QueryBuilder\Templates\SQL;

class ImplNestedWhere extends WhereQuery
{
	public function __construct(ActiveRecord $parent, callable $callback)
	{
		$record = $callback(new ClientNestedCondition());
		if (!($record instanceof ActiveRecord)) {
			DBFacade::dumpException($this, 'Callback should return ActiveRecord implement!', func_get_args());
		}

		$callbackQueryBox = $record->getQueryBox();
		parent::__construct(
			$this->createQueryBox(
				template: SQL::NESTED_WHERE,
				clearArgs: [trim($callbackQueryBox->getQuerySnapshot())],
				dryArgs: $callbackQueryBox->getDryArgs(),
				parentBox: $parent->getQueryBox()
			)
		);
	}
}