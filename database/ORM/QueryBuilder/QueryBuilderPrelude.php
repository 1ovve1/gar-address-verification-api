<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder;

use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\QueryTypes\{Delete\DeleteAble,
	Delete\DeleteTrait,
	Insert\InsertAble,
	Insert\InsertTrait,
	Select\SelectAble,
	Select\SelectTrait,
	Update\UpdateAble,
	Update\UpdateTrait};
use DB\ORM\QueryBuilder\Utils\ActiveRecord;
use DB\ORM\QueryBuilder\Utils\ActiveRecordImpl;

/**
 * Common interface for query builder
 *
 * @phpstan-import-type DatabaseContract from \DB\ORM\DBAdapter\DBAdapter
 */
abstract class QueryBuilderPrelude
	extends ActiveRecordImpl
	implements SelectAble, InsertAble, UpdateAble, DeleteAble, BuilderOptions
{
use SelectTrait, InsertTrait, UpdateTrait, DeleteTrait;

	/**
	 * {@inheritDoc}
	 */
	public static function findFirst(string $field,
	                                 mixed $value,
	                                 ?string $anotherTable = null): array
	{
		return static::select($field, $anotherTable)->where($field, $value)->save();
	}


}