<?php declare(strict_types=1);

namespace Tests\Unit\QueryBuilder;

use DB\ORM\QueryBuilder\ActiveRecord\QueryBox;
use DB\ORM\Resolver\DBResolver;
use PHPUnit\Framework\TestCase;

defined('COND_EQ') ?:
	define('COND_EQ', DBResolver::cond_eq());

class QueryTypesTestCase extends TestCase
{
	/**
	 * @param string $expectedQuery
	 * @param QueryBox $queryBox
	 * @param array<mixed>|null $args
	 * @return void
	 */
	function compare(string $expectedQuery, QueryBox $queryBox, ?array $args = null): void
	{
		$querySnapshot = trim($queryBox->getQuerySnapshot());
		$querySnapshot = trim($querySnapshot, DBResolver::fmtSep());

		$expectedQuery = trim($expectedQuery);
		$expectedQuery = trim($expectedQuery);

		$this->assertEquals($expectedQuery, $querySnapshot);
		if (null !== $args) {
			$this->assertEquals($args, $queryBox->getDryArgs());
		}
	}
}