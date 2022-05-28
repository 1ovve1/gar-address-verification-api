<?php declare(strict_types=1);

namespace Tests\Integration;


class AddressNameTest extends BaseTestSetup
{
	public function testFullAddressWithHouses() 
	{
		$response = $this->runApp('GET', '/address', 'address=калм,лаган,кр,кра,школьная');
		$data = (string) $response->getBody();

		$data = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
    		return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
		}, $data);


		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
		$this->assertStringContainsString('objectid', $data);
		$this->assertStringContainsString('typename', $data);
		$this->assertStringContainsString('name', $data);
		$this->assertStringContainsString('houses', $data);
		$this->assertStringContainsString('house', $data);
		$this->assertStringContainsString('калм', $data);
		$this->assertStringContainsString('лаган', $data);
		$this->assertStringContainsString('кр', $data);
		$this->assertStringContainsString('кра', $data);
		$this->assertStringContainsString('школьная', $data);
		$this->assertStringNotContainsString('variants', $data);
		$this->assertStringNotContainsString('parent', $data);
		$this->assertStringNotContainsString('parent_variants', $data);
		$this->assertStringNotContainsString('error', $data);
	}	

	public function testEmptyInput() {
		$response = $this->runApp('GET', '/address', 'address=');
		$data = (string) $response->getBody();

		$data = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
    		return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
		}, $data);


		$this->assertEquals(200, $response->getStatusCode());
		$this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
		$this->assertStringContainsString('objectid', $data);
		$this->assertStringContainsString('typename', $data);
		$this->assertStringContainsString('name', $data);
		$this->assertStringContainsString('variants', $data);
		$this->assertStringNotContainsString('parent', $data);
		$this->assertStringNotContainsString('parent_variants', $data);
		$this->assertStringNotContainsString('error', $data);
	}

	public function testAddressNotFound() 
	{
		$response = $this->runApp('GET', '/address', 'address=Пушкино,Колотушкино');
		$data = (string) $response->getBody();

		$data = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
    		return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
		}, $data);


		$this->assertEquals(404, $response->getStatusCode());
		$this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
		$this->assertStringNotContainsString('objectid', $data);
		$this->assertStringNotContainsString('typename', $data);
		$this->assertStringNotContainsString('name', $data);
		$this->assertStringNotContainsString('houses', $data);
		$this->assertStringNotContainsString('house', $data);
		$this->assertStringNotContainsString('variants', $data);
		$this->assertStringNotContainsString('parent', $data);
		$this->assertStringNotContainsString('parent_variants', $data);
		$this->assertStringContainsString('error', $data);
	}

	public function testTooLargeAddressIncorectInput() 
	{
		$response = $this->runApp('GET', '/address', 'address=' . str_repeat('Москва', 1000));
		$data = (string) $response->getBody();

		$data = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
    		return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
		}, $data);


		$this->assertEquals(414, $response->getStatusCode());
		$this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
		$this->assertStringNotContainsString('objectid', $data);
		$this->assertStringNotContainsString('typename', $data);
		$this->assertStringNotContainsString('name', $data);
		$this->assertStringNotContainsString('houses', $data);
		$this->assertStringNotContainsString('house', $data);
		$this->assertStringNotContainsString('variants', $data);
		$this->assertStringNotContainsString('parent', $data);
		$this->assertStringNotContainsString('parent_variants', $data);
		$this->assertStringContainsString('error', $data);
	}	

	public function testWithWrongParamIncorectInput() 
	{
		$response = $this->runApp('GET', '/address', 'name=калм,лаган,кр,кра,школьная');
		$data = (string) $response->getBody();

		$data = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
    		return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
		}, $data);


		$this->assertEquals(406, $response->getStatusCode());
		$this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
		$this->assertStringNotContainsString('objectid', $data);
		$this->assertStringNotContainsString('typename', $data);
		$this->assertStringNotContainsString('name', $data);
		$this->assertStringNotContainsString('houses', $data);
		$this->assertStringNotContainsString('house', $data);
		$this->assertStringNotContainsString('калм', $data);
		$this->assertStringNotContainsString('лаган', $data);
		$this->assertStringNotContainsString('кр', $data);
		$this->assertStringNotContainsString('кра', $data);
		$this->assertStringNotContainsString('школьная', $data);
		$this->assertStringNotContainsString('variants', $data);
		$this->assertStringNotContainsString('parent', $data);
		$this->assertStringNotContainsString('parent_variants', $data);
		$this->assertStringContainsString('error', $data);
	}	
}