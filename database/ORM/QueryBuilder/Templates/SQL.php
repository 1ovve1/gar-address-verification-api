<?php

namespace DB\ORM\QueryBuilder\Templates;

enum SQL: string
{
	case SELECT = 'SELECT %s FROM %s';

	case WHERE = 'WHERE %s %s (?)';
	case NESTED_WHERE = 'WHERE (%s)';
	case WHERE_AND = 'AND %s %s (?)';
	case WHERE_NESTED_AND = 'AND (%s)';
	case WHERE_OR = 'OR %s %s (?)';
	case WHERE_NESTED_OR = 'OR (%s)';

	case INNER_JOIN = 'INNER JOIN %s ON %s = %s';
	case LEFT_JOIN = 'LEFT OUTER JOIN %s ON %s = %s';
	case RIGHT_JOIN = 'RIGHT OUTER JOIN %s ON %s = %s';

	case LIMIT = 'LIMIT %s';

	case ORDER_BY_ASK = 'ORDER BY %s ASC';
	case ORDER_BY_DESK = 'ORDER BY %s DESC';

	case INSERT = 'INSERT INTO %s (%s) VALUES %s';

	case UPDATE = 'UPDATE %s SET %s = (?)';

	case DELETE = 'DELETE FROM %s';

	case EMPTY = '';
	case NESTED_CONDITION = '(%s)';
	case CONDITION = '%s %s (?)';
	case SEPARATOR = ' ';

	case PSEUDONYMS_FIELDS = '.';
	case PSEUDONYMS_TABLES = ' as ';
}