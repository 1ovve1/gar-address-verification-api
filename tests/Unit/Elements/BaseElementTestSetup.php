<?php declare(strict_types=1);

namespace Tests\Unit\Elements;

use GAR\Repository\Elements\Element;
use PHPUnit\Framework\TestCase;

class BaseElementTestSetup extends TestCase
{
	const SIMPLE_DATA_CASE = [123, 'wewe', 'wew'];
	const EMPTY_DATA_CASE = [];

	function prepareAndMakeElementTest(string $elementClass): void
	{
		$element = $elementClass::fromQueryResult(self::SIMPLE_DATA_CASE);
		$this->makeTestElementByExpect($element, self::SIMPLE_DATA_CASE);

		$this->expectException(\RuntimeException::class);
		$elementClass::fromQueryResult(self::EMPTY_DATA_CASE);
	}

	/**
	 * @param Element $element
	 * @param array<mixed> $expect
	 * @return void
	 */
	function makeTestElementByExpect(Element $element, array $expect): void
	{
		$data = $element->getData();

		$this->assertSameSize($data, $expect, 'length of element are different');

		foreach ($expect as $element) {
			$this->assertContains($element, $data, "data element was not fount ({$element})");
		}

	}
}