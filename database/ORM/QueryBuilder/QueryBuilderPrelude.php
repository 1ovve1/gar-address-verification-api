<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder;

use DB\ORM\DBAdapter\QueryResult;
use DB\ORM\DBAdapter\QueryTemplate;
use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;
use DB\ORM\QueryBuilder\QueryTypes\{Delete\DeleteAble,
	Delete\DeleteTrait,
	Insert\InsertAble,
	Insert\InsertTrait,
	Select\SelectAble,
	Select\SelectTrait,
	Update\UpdateAble,
	Update\UpdateTrait};

/**
 * Common interface for query builder
 *
 * @phpstan-import-type DatabaseContract from \DB\ORM\DBAdapter\DBAdapter
 */
abstract class QueryBuilderPrelude
	implements SelectAble, InsertAble, UpdateAble, DeleteAble, BuilderOptions
{
use SelectTrait, InsertTrait, UpdateTrait, DeleteTrait;

	/** @var ActiveRecord[] */
	private readonly array $userStates;
	/** @var QueryTemplate - force insert template */
	private readonly QueryTemplate $forceInsertTemplate;

	/**
	 * @param array<string> $fields
	 * @param string|null $tableName
	 */
	public function __construct(array $fields = [],
	                            ?string $tableName = null)
	{
		$this->userStates = $this->prepareStates();

		$db = DBFacade::getDBInstance();

		$tableName ??= DBFacade::genTableNameByClassName(static::class);

		$this->forceInsertTemplate = $db->getForceInsertTemplate(
			tableName: $tableName,
			fields: $fields,
			stagesCount: (int)$_ENV['DB_BUFF']
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public static function findFirst(string $field,
	                                 mixed $value,
	                                 ?string $anotherTable = null): array
	{
		return static::select($field, $anotherTable)->where($field, $value)->save();
	}

	/**
	 * Here you can declare states that you want to use in your pseudo-model
	 *
	 * @return ActiveRecord[]
	 */
	protected function prepareStates(): array
	{
		return [];
	}

	/**
	 * Execute template by name
	 * @param string $templateName
	 * @param array<mixed> $queryArguments
	 * @return array<mixed>|false|null
	 */
	public function __call(string $templateName, array $queryArguments)
	{
		$state = $this->userStates[$templateName] ?? null;

		if (null === $state) {
			throw new \RuntimeException('Unknown state');
		}

		return $state->execute($queryArguments);
	}

	/**
	 * @inheritDoc
	 */
	public function forceInsert(array $values): QueryResult
	{
		return $this->forceInsertTemplate->exec($values);
	}

	/**
	 * @inheritDoc
	 */
	public function saveForceInsert(): QueryResult
	{
		return $this->forceInsertTemplate->save();
	}


}