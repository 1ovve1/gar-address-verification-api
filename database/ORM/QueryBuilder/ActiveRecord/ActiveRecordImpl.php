<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\ActiveRecord;

use DB\ORM\DBAdapter\QueryTemplate;
use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\Templates\SQL;

abstract class ActiveRecordImpl implements ActiveRecord
{
	/** @var QueryBox - query container */
	public readonly QueryBox $queryBox;
	/** @var QueryTemplate template of current queryBox */
	public readonly QueryTemplate $state;

	public function __construct(QueryBox $queryBox)
	{
		$this->queryBox = $queryBox;
		$this->state = self::getState($queryBox);
	}

	/**
	 * Generate QueryTemplate by QueryBox
	 *
	 * @param QueryBox $queryBox
	 * @return QueryTemplate
	 */
	private static function getState(QueryBox $queryBox) : QueryTemplate
	{
		$db = DBFacade::getDBInstance();
		$template = $queryBox->querySnapshot;

		return $db->prepare($template);
	}

	/**
	 * {@inheritDoc}
	 */
	public function execute(array $values): array|false|null
	{
		return $this->state->exec($values)->fetchAll();
	}

	/**
	 * {@inheritDoc}
	 */
	public function save(): array|false|null
	{
		return $this->state->exec($this->queryBox->dryArgs)->fetchAll();
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