<?php declare(strict_types=1);

namespace Tests\Unit\Collection;

use GAR\Repository\Collections\AddressObjectCollection;
use GAR\Repository\Collections\HouseCollection;

class CollectionsTest extends BaseCollectionTestSetup
{

	function testAddressObjectCollection(): void
	{
		$this->prepareAndMakeTestWithCollection(AddressObjectCollection::class);
	}

	function testHouseCollection(): void
	{
		$this->prepareAndMakeTestWithCollection(HouseCollection::class);
	}
}