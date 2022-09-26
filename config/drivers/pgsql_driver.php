<?php declare(strict_types=1);

use DB\ORM\QueryBuilder\QueryTypes\{ContinueWhere\ImplNestedWhereAnd,
	ContinueWhere\ImplNestedWhereOr,
	ContinueWhere\ImplWhereAnd,
	ContinueWhere\ImplWhereOr,
	Delete\ImplDelete,
	Insert\ImplInsert,
	Join\ImplInnerJoin,
	Join\ImplLeftJoin,
	Join\ImplRightJoin,
	Limit\ImplLimit,
	NestedCondition\ImplNestedCondition,
	NestedCondition\ImplNestedConditionAnd,
	NestedCondition\ImplNestedConditionOr,
	NestedCondition\ImplNestedInNested,
	NestedCondition\ImplNestedInNestedAnd,
	NestedCondition\ImplNestedInNestedOr,
	OrderBy\ImplOrderBy,
	Select\ImplSelect,
	Update\ImplUpdate,
	Where\ImplNestedWhere,
	Where\ImplWhere};
use DB\ORM\QueryBuilder\Templates\DBResolver;

/**
 * @return array<string, array<string, string>>
 */
return [
	DBResolver::CONDITIONS => [
		'=' => '=',
		'<=' => '<=',
		'>=' => '>=',
		'<' => '<',
		'>' => '>',
		'LIKE' => 'ILIKE',
		'ILIKE' => 'ILIKE'
	],
	DBResolver::SQL => [
		ImplSelect::class => 'SELECT %s FROM %s',

		ImplWhere::class => 'WHERE %s %s (?)',
		ImplWhereAnd::class => 'AND %s %s (?)',
		ImplWhereOr::class => 'OR %s %s (?)',

		ImplNestedWhere::class => 'WHERE (%s)',
		ImplNestedWhereAnd::class => 'AND (%s)',
		ImplNestedWhereOr::class => 'OR (%s)',

		ImplInnerJoin::class => 'INNER JOIN %s ON %s = %s',
		ImplLeftJoin::class => 'LEFT OUTER JOIN %s ON %s = %s',
		ImplRightJoin::class => 'RIGHT OUTER JOIN %s ON %s = %s',

		ImplLimit::class => 'LIMIT %s',
		ImplOrderBy::class => 'ORDER BY %s %s',

		ImplInsert::class => 'INSERT INTO %s (%s) VALUES %s',
		ImplUpdate::class => 'UPDATE %s SET %s = (?)',
		ImplDelete::class => 'DELETE FROM %s',

		ImplNestedCondition::class => '%s %s (?)',
		ImplNestedConditionAnd::class => 'AND %s %s (?)',
		ImplNestedConditionOr::class => 'OR %s %s (?)',

		ImplNestedInNested::class => '(%s)',
		ImplNestedInNestedAnd::class => 'AND (%s)',
		ImplNestedInNestedOr::class => 'OR (%s)',
	],
	DBResolver::FMT => [
		DBResolver::SEPARATOR => ' ',
		DBResolver::PSEUDONYMS_FIELDS => '.',
		DBResolver::PSEUDONYMS_TABLES => ' as ',
	]
];