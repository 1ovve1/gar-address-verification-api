<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Select;

use DB\Exceptions\Unchecked\BadQueryBuilderCallbackReturnExcpetion;
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;
use DB\ORM\QueryBuilder\ActiveRecord\QueryBox;

class ImplSubSelect extends SelectQuery
{
	public function __construct(string $fields,
	                            callable $callback)
	{
		$record = $callback();
		if (!is_a($record, ActiveRecord::class)) {
			throw new BadQueryBuilderCallbackReturnExcpetion($this);
		} else {
			/** @var QueryBox $queryBox */
			$queryBox = $record->getQueryBox();
		}

		$subQuery = trim($queryBox->getQuerySnapshot());
		$subQueryArgs = $queryBox->getDryArgs();

		parent::__construct($this::createQueryBox(
			clearArgs: [$fields, $subQuery],
			dryArgs: $subQueryArgs
		));
	}

}