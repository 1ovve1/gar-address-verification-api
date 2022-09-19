<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\NestedCondition;

use DB\Exceptions\Unchecked\BadQueryBuilderCallbackReturnExcpetion;
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;

class ImplNestedInNestedAnd extends NestedContinueConditionQuery
{
	public function __construct(ActiveRecord $parent, callable $callback)
	{
		$record = $callback(new ClientNestedCondition());
		if (!($record instanceof ActiveRecord)) {
			throw new BadQueryBuilderCallbackReturnExcpetion($record);
		}

		$callbackQueryBox = $record->getQueryBox();
		parent::__construct(
			$this->createQueryBox(
				clearArgs: [trim($callbackQueryBox->getQuerySnapshot())],
				dryArgs: $callbackQueryBox->getDryArgs(),
				parentBox: $parent->getQueryBox()
			)
		);
	}
}