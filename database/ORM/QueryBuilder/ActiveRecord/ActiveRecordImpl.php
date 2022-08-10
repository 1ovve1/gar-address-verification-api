<?php

namespace DB\ORM\QueryBuilder\ActiveRecord;

use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\Templates\SQL;

abstract class ActiveRecordImpl implements ActiveRecord
{
	public readonly QueryBox $queryBox;

	public function __construct(QueryBox $queryBox)
	{
		$this->queryBox = $queryBox;
	}

	/**
	 * {@inheritDoc}
	 */
	public function execute(array $values): array
	{
		$db = DBFacade::getDBInstance();
		$state = $db->prepare($this->queryBox->querySnapshot);
		return $state->exec($values)->fetchAll();
	}

	/**
	 * {@inheritDoc}
	 */
	public function save(): array
	{
		$db = DBFacade::getDBInstance();
		$state = $db->prepare($this->queryBox->querySnapshot);
		return $state->exec($this->queryBox->dryArgs)->fetchAll();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getQueryBox(): QueryBox
	{
		return $this->queryBox;
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
	                                         ?QueryBox $parentBox = null): QueryBox
	{
		return new QueryBox($template, $clearArgs, $dryArgs, $parentBox);
	}
}