<?php

declare(strict_types=1);

namespace Tests\Integration;

use GAR\Helpers\ResponseCodes;

class AddressNameTest extends BaseTestSetup
{
	public function testSingleWordAddressWithSingleResult() : void
	{
		$param = 'калмыкия';
		$paramWithProp = 'address=' . $param;
		$response = $this->runApp('GET', '/address', $paramWithProp);

		$this->assertResponse(
			response: $response,
			code: ResponseCodes::OK_200,
			contains: ['"калмыкия"'],
			notContains: ['variants', 'parent', 'houses'],
		);
	}

	public function testSingleWordAddressWithParent() : void
	{
		$param = 'лаганский м';
		$paramWithProp = 'address=' . $param;
		$response = $this->runApp('GET', '/address', $paramWithProp);

		$this->assertResponse(
			response: $response,
			code: ResponseCodes::OK_200,
			contains: ['parent', '"лаганский м"'],
			notContains: ['variants', 'houses'],
		);
	}

	public function testSingleWordAddressWithVariant() : void
	{
		$param = 'к';
		$paramWithProp = 'address=' . $param;
		$response = $this->runApp('GET', '/address', $paramWithProp);

		$this->assertResponse(
			response: $response,
			code: ResponseCodes::OK_200,
			contains: ['variants'],
			notContains: ['parent', 'houses', '"к"'],
		);
	}

	public function testEmptyInput() : void
	{
		$param = '';
		$paramWithProp = 'address=' . $param;
		$response = $this->runApp('GET', '/address', $paramWithProp);

		$this->assertResponse(
			response: $response,
			code: ResponseCodes::OK_200,
			contains: ['variants'],
			notContains: ['parent', 'houses'],
		);
	}

	public function testDoubleWordAddressWithAllVariant() : void
	{
		$param = 'ка,к найти предка без родителя';
		$paramWithProp = 'address=' . $param;
		$response = $this->runApp('GET', '/address', $paramWithProp);

		$this->assertResponse(
			response: $response,
			code: ResponseCodes::OK_200,
			contains: ['variants'],
			notContains: ['"ка"', '"к найти предка без родителя"', 'parent', 'houses'],
		);
	}

	public function testDoubleWordAddressWithSingleAddressAndVariant() : void
	{
		$param = 'респ калм,я';
		$paramWithProp = 'address=' . $param;
		$response = $this->runApp('GET', '/address', $paramWithProp);

		$this->assertResponse(
			response: $response,
			code: ResponseCodes::OK_200,
			contains: ['"респ калм"', 'variants'],
			notContains: ['"я"', 'parent', 'houses'],
		);
	}

	public function testDoubleWordAddressWithSingleAddressAndParentAndVariant() : void
	{
		$param = 'лаганский м,с';
		$paramWithProp = 'address=' . $param;
		$response = $this->runApp('GET', '/address', $paramWithProp);

		$this->assertResponse(
			response: $response,
			code: ResponseCodes::OK_200,
			contains: ['"лаганский м"', 'variants', 'parent'],
			notContains: ['"с"', 'houses'],
		);
	}

	public function testDoubleWordAddressWithTwoSingleAddresses() : void
	{
		$param = 'респ калм,яшкульск';
		$paramWithProp = 'address=' . $param;
		$response = $this->runApp('GET', '/address', $paramWithProp);

		$this->assertResponse(
			response: $response,
			code: ResponseCodes::OK_200,
			contains: ['"респ калм"', '"яшкульск"'],
			notContains: ['parent', 'houses', 'variant'],
		);
	}

	public function testDoubleWordAddressWithTwoSingleAddressesAndParent() : void
	{
		$param = 'лаганский м,север';
		$paramWithProp = 'address=' . $param;
		$response = $this->runApp('GET', '/address', $paramWithProp);

		$this->assertResponse(
			response: $response,
			code: ResponseCodes::OK_200,
			contains: ['"лаганский м"', '"север"', 'parent'],
			notContains: ['variants', 'houses'],
		);
	}

	public function testComplexAddressWithSingleResult() : void
	{
		$param = 'калм,лаган,кр,кра,школьная';
		$paramWithProp = 'address=' . $param;
		$response = $this->runApp('GET', '/address', $paramWithProp);

		$this->assertResponse(
			$response,
			ResponseCodes::OK_200,
			explode(',', $param),
			['houses', 'variants', 'parent'],
			true,
		);
	}

	public function testComplexAddressWithHouses() : void
    {
	    $param = 'калм,лаган,кр,кра,школьная,';
	    $paramWithProp = 'address=' . $param;
	    $response = $this->runApp('GET', '/address', $paramWithProp);

	    $this->assertResponse(
		    $response,
		    ResponseCodes::OK_200,
		    explode(',', $param),
		    ['variants', 'parent'],
		    true,
	    );
    }

	public function testComplexAddressWithVariants() : void
	{
		$param = 'калм,лаган,кр,кра,';
		$paramWithProp = 'address=' . $param;
		$response = $this->runApp('GET', '/address', $paramWithProp);

		$this->assertResponse(
			response: $response,
			code: ResponseCodes::OK_200,
			contains: explode(',', $param),
			notContains: ['houses', 'parent'],
			jsonFlag: true,
		);
	}



    public function testAddressNotFound(): void
    {
	    $param = 'Пушкино,Колотушкино';
	    $paramWithProp = 'address=' . $param;
	    $response = $this->runApp('GET', '/address', $paramWithProp);

	    $this->assertResponse(
		    response: $response,
		    code: ResponseCodes::NOT_FOUND_404,
		    notContains: explode(',', $param),
		    errorFlag: true
	    );
    }

    public function testTooLargeAddressIncorectInput(): void
    {
	    $param = str_repeat('Москва', 1000);
	    $paramWithProp = 'address=' . $param;
	    $response = $this->runApp('GET', '/address', $paramWithProp);

	    $this->assertResponse(
		    response: $response,
		    code: ResponseCodes::PRECONDITION_FAILED_412,
		    notContains: [$param],
		    errorFlag: true
	    );
    }

    public function testWithWrongParamIncorrectInput() : void
    {
	    $param = 'Great Brittan, London';
	    $paramWithProp = 'address=' . $param;
	    $response = $this->runApp('GET', '/address', $paramWithProp);

	    $this->assertResponse(
		    response: $response,
		    code: ResponseCodes::PRECONDITION_FAILED_412,
		    notContains: explode(',', $param),
		    errorFlag: true
	    );
    }
}
