<?php declare(strict_types=1);

namespace DB\ORM\Resolver;

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
	Condition\ImplCondition,
	Condition\ImplConditionAnd,
	Condition\ImplConditionOr,
	Condition\ImplNestedCondition,
	Condition\ImplNestedConditionAnd,
	Condition\ImplNestedConditionOr,
	OrderBy\ImplOrderBy,
	Select\ImplSelect,
	Update\ImplUpdate,
	Where\ImplNestedWhere,
	Where\ImplWhere};

class AST
{
	const COND = 'conditions';
	const COND_EQ = '=';
	const COND_EQL = '<=';
	const COND_EQH = '>=';
	const COND_L = '<';
	const COND_H = '>';
	const COND_LIKE = 'LIKE';
	const COND_ILIKE = 'ILIKE';


	const SQL = 'sql';
	const SQL_SELECT = ImplSelect::class;

	const SQL_INSERT = ImplInsert::class;
	const SQL_UPDATE = ImplUpdate::class;
	const SQL_DELETE = ImplDelete::class;

	const SQL_WHERE = ImplWhere::class;
	const SQL_WHERE_AND = ImplWhereAnd::class;
	const SQL_WHERE_OR = ImplWhereOr::class;
	const SQL_WHERE_NESTED = ImplNestedWhere::class;
	const SQL_WHERE_NESTED_AND = ImplNestedWhereAnd::class;
	const SQL_WHERE_NESTED_OR = ImplNestedWhereOr::class;

	const SQL_COND = ImplCondition::class;
	const SQL_COND_AND = ImplConditionAnd::class;
	const SQL_COND_OR = ImplConditionOr::class;

	const SQL_COND_NESTED = ImplNestedCondition::class;
	const SQL_COND_NESTED_AND = ImplNestedConditionAnd::class;
	const SQL_COND_NESTED_OR = ImplNestedConditionOr::class;

	const SQL_JOIN_INNER = ImplInnerJoin::class;
	const SQL_JOIN_LEFT = ImplLeftJoin::class;
	const SQL_JOIN_RIGHT = ImplRightJoin::class;

	const SQL_LIMIT = ImplLimit::class;
	const SQL_ORDER_BY = ImplOrderBy::class;

	const FMT = 'fmt';
	const FMT_SEP = ' ';
	const FMT_PS_FIELDS = '.';
	const FMT_PS_TABLES = ' as ';
}