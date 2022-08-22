<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\ContinueWhere;

use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\QueryTypes\NestedCondition\ClientNestedCondition;
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;
use DB\ORM\QueryBuilder\ActiveRecord\SQLNestedWhereConstructor;
use DB\ORM\QueryBuilder\Templates\SQL;

class ImplNestedWhereAnd extends ContinueWhereQuery
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
				template: SQL::WHERE_NESTED_AND,
				clearArgs: [trim($callbackQueryBox->querySnapshot)],
				dryArgs: $callbackQueryBox->dryArgs,
				parentBox: $parent->getQueryBox()
			)
		);
	}
}