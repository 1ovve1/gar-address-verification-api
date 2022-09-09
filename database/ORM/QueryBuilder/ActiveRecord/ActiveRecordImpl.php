<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\ActiveRecord;

use DB\Exceptions\Unchecked\FailedDBConnectionWithDBException;
use DB\ORM\DBAdapter\QueryResult;
use DB\ORM\DBAdapter\QueryTemplate;
use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\Templates\SQL;

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
	 * @throws FailedDBConnectionWithDBException
	 */
	private static function getState(QueryBox $queryBox) : QueryTemplate
	{
		$db = DBFacade::getDBInstance();
		$template = $queryBox->querySnapshot;

		return $db->prepare($template);
	}

	/**
	 * {@inheritDoc}
	 * @throws FailedDBConnectionWithDBException
	 */
	public function execute(array $values): QueryResult
	{
		$state = self::getState($this->queryBox);
		return $state->exec($values);
	}

	/**
	 * {@inheritDoc}
	 * @throws FailedDBConnectionWithDBException
	 */
	public function save(): QueryResult
	{
		$state = self::getState($this->queryBox);
		return $state->exec($this->queryBox->dryArgs);
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