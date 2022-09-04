<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\NestedCondition;

use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\Templates\SQL;
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;

class ImplNestedInNested extends NestedConditionQuery
{
	public function __construct(callable $callback)
	{
		$record = $callback(new ClientNestedCondition());
		if (!($record instanceof ActiveRecord)) {
			DBFacade::dumpException($this, 'Callback should return ActiveRecord implement!', func_get_args());
		}

		$callbackQueryBox = $record->getQueryBox();
		parent::__construct(
			$this->createQueryBox(
				template: SQL::NESTED_CONDITION,
				clearArgs: [trim($callbackQueryBox->querySnapshot)],
				dryArgs: $callbackQueryBox->dryArgs
			)
		);
	}
}