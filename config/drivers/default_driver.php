<?php declare(strict_types=1);

use DB\ORM\Resolver\AST;

/**
 * @return array<string, array<string, string>>
 */
return [
	AST::COND => [
		AST::COND_EQ => '=',
		AST::COND_EQL => '<=',
		AST::COND_EQH => '>=',
		AST::COND_L => '<',
		AST::COND_H => '>',
		AST::COND_LIKE => 'LIKE',
		AST::COND_ILIKE => 'ILIKE'
	],

	AST::SQL => [
		AST::SQL_SELECT => 'SELECT %s FROM %s',
		AST::SQL_SUB_SELECT => 'SELECT %s FROM (%s)',

		AST::SQL_WHERE => 'WHERE %s %s (?)',
		AST::SQL_WHERE_AND => 'AND %s %s (?)',
		AST::SQL_WHERE_OR => 'OR %s %s (?)',

		AST::SQL_WHERE_NESTED => 'WHERE (%s)',
		AST::SQL_WHERE_NESTED_AND => 'AND (%s)',
		AST::SQL_WHERE_NESTED_OR => 'OR (%s)',

		AST::SQL_JOIN_INNER => 'INNER JOIN %s ON %s = %s',
		AST::SQL_JOIN_LEFT => 'LEFT OUTER JOIN %s ON %s = %s',
		AST::SQL_JOIN_RIGHT => 'RIGHT OUTER JOIN %s ON %s = %s',

		AST::SQL_LIMIT => 'LIMIT %s',
		AST::SQL_ORDER_BY => 'ORDER BY %s %s',

		AST::SQL_INSERT => 'INSERT INTO %s (%s) VALUES %s',
		AST::SQL_UPDATE => 'UPDATE %s SET %s = (?)',
		AST::SQL_DELETE => 'DELETE FROM %s',

		AST::SQL_COND => '%s %s (?)',
		AST::SQL_COND_AND => 'AND %s %s (?)',
		AST::SQL_COND_OR => 'OR %s %s (?)',

		AST::SQL_COND_NESTED => '(%s)',
		AST::SQL_COND_NESTED_AND => 'AND (%s)',
		AST::SQL_COND_NESTED_OR => 'OR (%s)',
	],
	AST::FMT => [
		AST::FMT_SEP => ' ',
		AST::FMT_PS_FIELDS => '.',
		AST::FMT_PS_TABLES => ' as ',
	]
];