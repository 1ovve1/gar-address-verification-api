<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\ActiveRecord;

use DB\ORM\DBAdapter\QueryResult;
use DB\ORM\DBAdapter\QueryTemplate;
use DB\ORM\DBFacade;
use DB\ORM\Resolver\DBResolver;

abstract class ActiveRecordImpl implements ActiveRecord
{
	/** @var QueryBox - query container */
	public readonly QueryBox $queryBox;

	public function __construct(QueryBox $queryBox)
	{
		$this->queryBox = $queryBox;
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
		$template = $queryBox->getQuerySnapshot();

		return $db->prepare($template);
	}

	/**
	 * {@inheritDoc}
	 */
	public function execute(array $values): QueryResult
	{
		$state = self::getState($this->queryBox);
		return $state->exec($values);
	}

	/**
	 * {@inheritDoc}
	 */
	public function save(): QueryResult
	{
		$state = self::getState($this->queryBox);
		return $state->exec($this->queryBox->getDryArgs());
	}

	/**
	 * {@inheritDoc}
	 */
	public function getQueryBox(): QueryBox
	{
		return $this->queryBox;
	}

	/**
	 * @param array<string|int> $clearArgs
	 * @param array<DatabaseContract> $dryArgs
	 * @param QueryBox|null $parentBox
	 * @return QueryBox
	 */
	protected static function createQueryBox(array     $clearArgs = [],
	                                         array     $dryArgs = [],
	                                         ?QueryBox $parentBox = null): QueryBox
	{
		$template = DBResolver::sql(static::class);

		return new QueryBox($template, $clearArgs, $dryArgs, $parentBox);
	}
}