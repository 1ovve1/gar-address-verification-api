<?php

declare(strict_types=1);

namespace Tests\Integration;

use GAR\Helpers\ResponseCodes;

class AddressCodeTest extends BaseTestSetup
{
	const REGION = '8';
	
    public function testAllCodesByName(): void
    {
	    $param = 'калм,лаган,кр,кра,школьная';
	    $paramWithProp = 'address=' . $param;
	    $response = $this->runApp('GET', '/' . self::REGION . '/code/all', $paramWithProp);

	    $this->assertResponse(
		    response: $response,
		    code: ResponseCodes::OK_200,
		    contains: ['"OKATO"', '"OKTMO"', '"CODE"'],
	    );
    }

    public function testAllCodesByObjectId(): void
    {
	    $param = '109874';
	    $paramWithProp = 'objectid=' . $param;
	    $response = $this->runApp('GET', '/' . self::REGION . '/code/all', $paramWithProp);

	    $this->assertResponse(
		    response: $response,
		    code: ResponseCodes::OK_200,
		    contains: ['"OKATO"', '"OKTMO"', '"CODE"'],
	    );
    }

    public function testOkatoByName(): void
    {
	    $param = 'калм,лаган,кр,кра,школьная';
	    $paramWithProp = 'address=' . $param;
	    $response = $this->runApp('GET', '/' . self::REGION . '/code/okato', $paramWithProp);

	    $this->assertResponse(
		    response: $response,
		    code: ResponseCodes::OK_200,
		    contains: ['"OKATO"'],
		    notContains: ['"OKTMO"', '"CODE"']
	    );
    }

    public function testOkatoByObjectId(): void
    {
	    $param = '109874';
	    $paramWithProp = 'objectid=' . $param;
	    $response = $this->runApp('GET', '/' . self::REGION . '/code/okato', $paramWithProp);

	    $this->assertResponse(
		    response: $response,
		    code: ResponseCodes::OK_200,
		    contains: ['"OKATO"'],
		    notContains: ['"OKTMO"', '"CODE"']
	    );
    }

    public function testOktmoByName(): void
    {
	    $param = 'калм,лаган,кр,кра,школьная';
	    $paramWithProp = 'address=' . $param;
	    $response = $this->runApp('GET', '/' . self::REGION . '/code/oktmo', $paramWithProp);

	    $this->assertResponse(
		    response: $response,
		    code: ResponseCodes::OK_200,
		    contains: ['"OKTMO"'],
		    notContains: ['"OKATO"', '"CODE"']
	    );
    }

    public function testOktmoByObjectId(): void
    {
	    $param = '109874';
	    $paramWithProp = 'objectid=' . $param;
	    $response = $this->runApp('GET', '/' . self::REGION . '/code/oktmo', $paramWithProp);

	    $this->assertResponse(
		    response: $response,
		    code: ResponseCodes::OK_200,
		    contains: ['"OKTMO"'],
		    notContains: ['"OKATO"', '"CODE"']
	    );
    }

    public function testKladrByName(): void
    {
	    $param = 'калм,лаган,кр,кра,школьная';
	    $paramWithProp = 'address=' . $param;
	    $response = $this->runApp('GET', '/' . self::REGION . '/code/code', $paramWithProp);

	    $this->assertResponse(
		    response: $response,
		    code: ResponseCodes::OK_200,
		    contains: ['"CODE"'],
		    notContains: ['"OKATO"', '"OKTMO"']
	    );
    }

    public function testKladrByObjectId(): void
    {
	    $param = '109874';
	    $paramWithProp = 'objectid=' . $param;
	    $response = $this->runApp('GET', '/' . self::REGION . '/code/code', $paramWithProp);

	    $this->assertResponse(
		    response: $response,
		    code: ResponseCodes::OK_200,
		    contains: ['"CODE"'],
		    notContains: ['"OKATO"', '"OKTMO"']
	    );
    }

    public function testWrongAddressName(): void
    {
	    $param = 'Пушкино,Колотушкино';
	    $paramWithProp = 'address=' . $param;
	    $response = $this->runApp('GET', '/' . self::REGION . '/code/code', $paramWithProp);

	    $this->assertResponse(
		    response: $response,
		    code: ResponseCodes::NOT_FOUND_404,
		    notContains: ['"CODE"', '"OKATO"', '"OKTMO"'],
		    errorFlag: true
	    );
    }

    public function testWrongObjectId(): void
    {
	    $param = '0';
	    $paramWithProp = 'objectid=' . $param;
	    $response = $this->runApp('GET', '/' . self::REGION . '/code/code', $paramWithProp);

	    $this->assertResponse(
		    response: $response,
		    code: ResponseCodes::NOT_FOUND_404,
		    notContains: ['"CODE"', '"OKATO"', '"OKTMO"'],
		    errorFlag: true
	    );
    }

    public function testIncorrectParamsName(): void
    {
	    $param = 'asdas';
	    $paramWithProp = 'sdsdsd=' . $param;
	    $response = $this->runApp('GET', '/' . self::REGION . '/code/code', $paramWithProp);

	    $this->assertResponse(
		    response: $response,
		    code: ResponseCodes::PRECONDITION_FAILED_412,
		    notContains: ['"CODE"', '"OKATO"', '"OKTMO"'],
		    errorFlag: true
	    );
    }
}
