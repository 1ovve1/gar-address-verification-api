<?php declare(strict_types=1);

namespace Tests\Integration;


class AddressCodeTest extends BaseTestSetup
{
  public function testAllCodesByName() 
  {
    $response = $this->runApp('GET', '/code/all', 'address=калм,лаган,кр,кра,школьная');
    $data = (string) $response->getBody();

    $data = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
    }, $data);


    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    $this->assertStringContainsString('OKATO', $data);
    $this->assertStringContainsString('OKTMO', $data);
    $this->assertStringContainsString('KLADR', $data);
    $this->assertStringNotContainsString('error', $data);
  } 

  public function testAllCodesByObjectId() 
  {
    $response = $this->runApp('GET', '/code/all', 'objectid=109874');
    $data = (string) $response->getBody();

    $data = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
    }, $data);


    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    $this->assertStringContainsString('OKATO', $data);
    $this->assertStringContainsString('OKTMO', $data);
    $this->assertStringContainsString('KLADR', $data);
    $this->assertStringNotContainsString('error', $data);
  }

  public function testOkatoByName() 
  {
    $response = $this->runApp('GET', '/code/okato', 'address=калм,лаган,кр,кра,школьная');
    $data = (string) $response->getBody();

    $data = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
    }, $data);


    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    $this->assertStringContainsString('OKATO', $data);
    $this->assertStringNotContainsString('OKTMO', $data);
    $this->assertStringNotContainsString('KLADR', $data);
    $this->assertStringNotContainsString('error', $data);
  } 

  public function testOkatoByObjectId() 
  {
    $response = $this->runApp('GET', '/code/okato', 'objectid=109874');
    $data = (string) $response->getBody();

    $data = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
    }, $data);


    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    $this->assertStringContainsString('OKATO', $data);
    $this->assertStringNotContainsString('OKTMO', $data);
    $this->assertStringNotContainsString('KLADR', $data);
    $this->assertStringNotContainsString('error', $data);
  }

  public function testOktmoByName() 
  {
    $response = $this->runApp('GET', '/code/oktmo', 'address=калм,лаган,кр,кра,школьная');
    $data = (string) $response->getBody();

    $data = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
    }, $data);


    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    $this->assertStringNotContainsString('OKATO', $data);
    $this->assertStringContainsString('OKTMO', $data);
    $this->assertStringNotContainsString('KLADR', $data);
    $this->assertStringNotContainsString('error', $data);
  } 

  public function testOktmoByObjectId() 
  {
    $response = $this->runApp('GET', '/code/oktmo', 'objectid=109874');
    $data = (string) $response->getBody();

    $data = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
    }, $data);


    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    $this->assertStringNotContainsString('OKATO', $data);
    $this->assertStringContainsString('OKTMO', $data);
    $this->assertStringNotContainsString('KLADR', $data);
    $this->assertStringNotContainsString('error', $data);
  }

  public function testKladrByName() 
  {
    $response = $this->runApp('GET', '/code/kladr', 'address=калм,лаган,кр,кра,школьная');
    $data = (string) $response->getBody();

    $data = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
    }, $data);


    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    $this->assertStringNotContainsString('OKATO', $data);
    $this->assertStringNotContainsString('OKTMO', $data);
    $this->assertStringContainsString('KLADR', $data);
    $this->assertStringNotContainsString('error', $data);
  } 

  public function testKladrByObjectId() 
  {
    $response = $this->runApp('GET', '/code/kladr', 'objectid=109874');
    $data = (string) $response->getBody();

    $data = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
    }, $data);


    $this->assertEquals(200, $response->getStatusCode());
    $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    $this->assertStringNotContainsString('OKATO', $data);
    $this->assertStringNotContainsString('OKTMO', $data);
    $this->assertStringContainsString('KLADR', $data);
    $this->assertStringNotContainsString('error', $data);
  }

  public function testWrongAddressName() 
  {
    $response = $this->runApp('GET', '/code/kladr', 'address=Пушкино,Колотушкино');
    $data = (string) $response->getBody();

    $data = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
    }, $data);


    $this->assertEquals(404, $response->getStatusCode());
    $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    $this->assertStringNotContainsString('OKATO', $data);
    $this->assertStringNotContainsString('OKTMO', $data);
    $this->assertStringNotContainsString('KLADR', $data);
    $this->assertStringContainsString('error', $data);
  }

  public function testWrongObjectId() 
  {
    $response = $this->runApp('GET', '/code/kladr', 'objectid=0');
    $data = (string) $response->getBody();

    $data = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
    }, $data);


    $this->assertEquals(404, $response->getStatusCode());
    $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    $this->assertStringNotContainsString('OKATO', $data);
    $this->assertStringNotContainsString('OKTMO', $data);
    $this->assertStringNotContainsString('KLADR', $data);
    $this->assertStringContainsString('error', $data);
  }

  public function testIncorectParamsName() 
  {
    $response = $this->runApp('GET', '/code/kladr', 'dfdf=0&sdsd=2');
    $data = (string) $response->getBody();

    $data = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
        return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UTF-16BE');
    }, $data);


    $this->assertEquals(406, $response->getStatusCode());
    $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
    $this->assertStringNotContainsString('OKATO', $data);
    $this->assertStringNotContainsString('OKTMO', $data);
    $this->assertStringNotContainsString('KLADR', $data);
    $this->assertStringContainsString('error', $data);  
  }
}