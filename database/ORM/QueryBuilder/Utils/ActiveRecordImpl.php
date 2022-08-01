<?php

namespace DB\ORM\QueryBuilder\Utils;

use DB\ORM\DBAdapter\DBAdapter;
use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\AbstractSQL\EndQuery;
use DB\ORM\QueryBuilder\Templates\SQL;

abstract class ActiveRecordImpl implements ActiveRecord
{
	private readonly DBAdapter $db;
	public readonly QueryBox $queryBox;

	public function __construct(QueryBox $queryBox)
	{
		$this->queryBox = $queryBox;
	}

	public function execute(array $values): array
	{
		$db = DBFacade::getInstance();
		$state = $db->prepare($this->queryBox->querySnapshot)->getTemplate();
		return $state->exec($values);
	}

	public function save(): array
	{
		$db = DBFacade::getInstance();
		$state = $db->prepare($this->queryBox->querySnapshot)->getTemplate();
		return $state->exec($this->queryBox->dryArgs);
	}

	public static function forceInsert(array $values, ?array $fields = null): EndQuery
	{
		return new EndQuerydf();
	}

	/**
	 * @param SQL $template
	 * @param array<mixed> $clearArgs
	 * @param array<mixed> $dryArgs
	 * @param QueryBox|null $parentBox
	 * @return QueryBox
	 */
	protected static function createQueryBox(SQL       $template,
	                                         array     $clearArgs = [],
	                                         array     $dryArgs = [],
	                                         ?QueryBox $parentBox = null): QueryBox {

		return new QueryBox($template, $clearArgs, $dryArgs, $parentBox);
	}

	public function getQueryBox(): QueryBox
	{
		return $this->queryBox;
	}
}