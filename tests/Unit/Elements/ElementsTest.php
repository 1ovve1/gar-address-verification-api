<?php declare(strict_types=1);

namespace Tests\Unit\Elements;

use GAR\Repository\Elements\AddressObjectElement;
use GAR\Repository\Elements\HouseElement;

class ElementsTest extends BaseElementTestSetup
{
	function testAddressObjectElement(): void
	{
		$this->prepareAndMakeElementTest(AddressObjectElement::class);
	}

	function testHouseElement(): void
	{
		$this->prepareAndMakeElementTest(HouseElement::class);
	}
}