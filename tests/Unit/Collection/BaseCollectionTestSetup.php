<?php declare(strict_types=1);

namespace Tests\Unit\Collection;

use GAR\Repository\Collections\Collection;
use PHPUnit\Framework\TestCase;

class BaseCollectionTestSetup extends TestCase
{
	const SIMPLE_ELEMENT_ONE = [
		'name' => 'sadasda',
		'typename' => 'sdfsd',
		'objectid' => 232323,
	];

	const SIMPLE_ELEMENT_TWO = [
		'name' => 'zzzzzz',
		'typename' => 'xxxxx',
		'objectid' => 00000,
	];

	const SINGLE_DATA_TEST = [
		self::SIMPLE_ELEMENT_ONE
	];

	const MULTIPLE_DATA_TEST = [

		self::SIMPLE_ELEMENT_ONE,
		self::SIMPLE_ELEMENT_TWO
	];

	const EMPTY_DATA_TEST = [];

	function prepareAndMakeTestWithCollection(string $collectionClass): void
	{

		$this->makeTestCollection(
			$collectionClass::fromQueryResult(self::SINGLE_DATA_TEST),
			self::SINGLE_DATA_TEST,
			true,
			true
		);

		$this->makeTestCollection(
			$collectionClass::fromQueryResult(self::MULTIPLE_DATA_TEST),
			self::MULTIPLE_DATA_TEST,
			false,
			true
		);

		$this->makeTestCollection(
			$collectionClass::fromQueryResult(self::EMPTY_DATA_TEST),
			self::EMPTY_DATA_TEST,
			false,
			false
		);
	}

	function makeTestCollection(Collection $collection,
								array $expectData,
	                            bool $isContainsOnlyOneElement,
	                            bool $isNotEmpty): void
	{
		$this->assertSameSize($expectData, $collection->getCollection());
		$this->assertSameSize($expectData, $collection->toArray());
		$this->assertEquals($isContainsOnlyOneElement, $collection->isContainsOnlyOneElement());
		$this->assertEquals($isNotEmpty, $collection->isNotEmpty());
	}

}