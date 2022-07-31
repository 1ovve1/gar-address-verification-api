<?php

namespace DB\ORM\Table\Utils;

use DB\ORM\DBAdapter\DBAdapter;
use DB\ORM\DBFacade;
use DB\ORM\Table\SQL\EndQuery;
use DB\ORM\Table\Templates\SQL;

trait ActiveRecordImpl
{
	private readonly DBAdapter $db;
	public readonly QueryBox $queryBox;

	public function execute(array $values): array
	{
		$db = DBFacade::getInstance();
		$state = $db->prepare($this->queryBox->getPreparedQueryFromQueryBox())->getTemplate();
		return $state->exec($values);
	}

	public function save(): array
	{
		$db = DBFacade::getInstance();
		$state = $db->prepare($this->queryBox->getPreparedQueryFromQueryBox())->getTemplate();
		return $state->exec($this->queryBox->getDryArgumentsList());
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
	 * @return void
	 */
	protected function initQueryBox(SQL       $template,
	                                array     $clearArgs = [],
	                                array     $dryArgs = [],
	                                ?QueryBox $parentBox = null) {
		if (isset($this->queryBox)) {
			DBFacade::dumpException($this, 'Try to rewrite QueryBox', func_get_args());
		}

		$this->queryBox = new QueryBox($template, $clearArgs, $dryArgs, $parentBox);
	}

	public function getQueryBox(): QueryBox
	{
		if (!isset($this->queryBox)) {
			DBFacade::dumpException($this, 'Try to get undefined QueryBox', func_get_args());
		}

		return $this->queryBox;
	}
}