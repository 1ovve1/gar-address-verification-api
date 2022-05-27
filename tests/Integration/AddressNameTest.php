<?php declare(strict_types=1);

namespace Tests\Integration;


class AddressNameTest extends BaseTestSetup
{
	public function testFullAddressWithHouses() 
	{
		$response = $this->runApp('GET', '/address', 'address=калм,лаган,кр,кра,школьная');
		var_dump((string)$response->getBody());
	}	
}