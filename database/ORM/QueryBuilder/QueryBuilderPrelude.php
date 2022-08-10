<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder;

use DB\ORM\DBAdapter\DBAdapter;
use DB\ORM\DBAdapter\QueryResult;
use DB\ORM\DBAdapter\QueryTemplate;
use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\ActiveRecord\QueryBox;
use DB\ORM\QueryBuilder\QueryTypes\{Delete\DeleteAble,
	Delete\DeleteTrait,
	Insert\InsertAble,
	Insert\InsertTrait,
	Select\SelectAble,
	Select\SelectTrait,
	Update\UpdateAble,
	Update\UpdateTrait};
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecordImpl;

/**
 * Common interface for query builder
 *
 * @phpstan-import-type DatabaseContract from \DB\ORM\DBAdapter\DBAdapter
 */
abstract class QueryBuilderPrelude
	implements SelectAble, InsertAble, UpdateAble, DeleteAble, BuilderOptions
{
use SelectTrait, InsertTrait, UpdateTrait, DeleteTrait;

	/** @var QueryTemplate - force insert template */
	private readonly QueryTemplate $forceInsertTemplate;

	/**
	 * @param array<string>|null $fields
	 * @param string|null $tableName
	 */
	public function __construct(?array $fields = null,
	                            ?string $tableName = null)
	{
		$db = DBFacade::getDBInstance();

		$tableName ??= DBFacade::genTableNameByClassName(static::class);
		$fields ??= static::getFields() ??
			throw new \RuntimeException('Unknown model fields: please override getFields method or put $fields into constructor params');

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
	 * Can be overridden in static class for forceInsert option
	 * @return ?array<string>
	 */
	protected static function getFields(): ?array
	{
		return null;
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