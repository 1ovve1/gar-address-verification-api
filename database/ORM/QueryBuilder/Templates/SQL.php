<?php

namespace DB\ORM\QueryBuilder\Templates;

enum SQL: string
{
	case SELECT = "SELECT %s FROM %s";
	case WHERE = "WHERE %s %s (?)";
	case WHERE_NESTED = "WHERE (%s)";
	case WHERE_AND = "AND %s %s (?)";
	case WHERE_OR = "OR %s %s (?)";
	case INNER_JOIN = "INNER JOIN %s on %s = %s";
	case LEFT_JOIN = "LEFT OUTER JOIN %s on %s = %s";
	case RIGHT_JOIN = "RIGHT OUTER JOIN %s on %s = %s";
	case LIMIT = "LIMIT %s";

	case GROUP_BY_ASK = "GROUP BY %s ASK";
	case GROUP_BY_DESK = "GROUP BY %s DESK";

	case EMPTY = '';

	case SEPARATOR = ' ';
}