<?php declare(strict_types=1);

namespace Tests\Unit\Builders;

use GAR\Storage\Builders\AddressBuilder;
use GAR\Storage\Builders\AddressBuilderDirector;
use GAR\Storage\Builders\AddressBuilderImplement;
use GAR\Storage\Collections\AddressObjectCollection;
use GAR\Storage\Collections\HouseCollection;
use PHPUnit\Framework\TestCase;
use Tests\Mock\FakeQueryResult;

class AddressBuilderImplementTODO extends TestCase
{

	const FIRST_NAME = 'Калмыкия';
	const FIRST_DATA = [
		0 => [
		"name" => "Калмыкия",
		"typename" => "Респ",
		"objectid" => 110423
		]
	];

	const SECOND_NAME = 'Лаганский';
	const SECOND_DATA = [
		0 => [
		"name" => "Лаганский",
		"typename" => "м.р-н",
		"objectid" => 95232638
		]
	];

	const THIRD_NAME = 'Джалыковское';
	const THIRD_DATA = [
		0 => [
		"name" => "Джалыковское",
		"typename" => "с.п.",
		"objectid" => 95232643
		]
	];

	const FOUR_VARIANT_DATA = [
		0 => [
			"name" => "Джалыково",
			"typename" => "с",
			"objectid" => 114333
		],
		1 => [
			"name" => "Буранное",
			"typename" => "с",
			"objectid" => 114417
		]
	];

	const HOUSES_DATA = [
		0 => [
			'name' => 1,
			'objectid' => 2222
		],
		1 => [
			'name' => 2,
			'objectid' => 4444
		]
	];

	const EXPECTED_ADDRESS = [
		0 => [
            self::FIRST_NAME => 
				self::FIRST_DATA,
		],
		1 => [
			self::SECOND_NAME => 
				self::SECOND_DATA,
		],
        2 => [
	        self::THIRD_NAME => 
				self::THIRD_DATA,
		],
		3 => [
			"variants" =>
				self::FOUR_VARIANT_DATA
		],
		4 => [
			'houses' =>
				self::HOUSES_DATA
		]
	];

	protected AddressBuilder $address;

	protected function setUp(): void
	{
		$this->address = new AddressBuilderImplement();
		parent::setUp();
	}


	public function testAddParentAddr(): void
	{
		$this->address
			->addParentAddr(self::THIRD_NAME, self::THIRD_DATA)
			->addParentAddr(self::SECOND_NAME, self::SECOND_DATA)
			->addParentAddr(self::FIRST_NAME, self::FIRST_DATA)
			->addChiledVariant(self::FOUR_VARIANT_DATA)
			->addChiledHouses(self::HOUSES_DATA);

		$result = $this->address->getAddress();

		$this->assertEquals(self::EXPECTED_ADDRESS, $result);
	}

	public function testAddChiledAddr(): void
	{
		$this->address
			->addChiledAddr(self::FIRST_NAME, self::FIRST_DATA)
			->addChiledAddr(self::SECOND_NAME, self::SECOND_DATA)
			->addChiledAddr(self::THIRD_NAME, self::THIRD_DATA)
			->addChiledVariant(self::FOUR_VARIANT_DATA)
			->addChiledHouses(self::HOUSES_DATA);

		$result = $this->address->getAddress();

		$this->assertEquals(self::EXPECTED_ADDRESS, $result);
	}

	public function testAddressBuilderDirector(): void
	{
		$director = new AddressBuilderDirector($this->address, [self::FIRST_NAME, self::SECOND_NAME, self::THIRD_NAME], 1, 2);

		$director->addParentAddr(new FakeQueryResult(self::SECOND_DATA))
				->addParentAddr(new FakeQueryResult(self::FIRST_DATA))
				->addChiledAddr(new FakeQueryResult(self::THIRD_DATA))
				->addChiledVariant(new FakeQueryResult(self::FOUR_VARIANT_DATA))
				->addChiledHouses(new FakeQueryResult(self::HOUSES_DATA));

		$result = $this->address->getAddress();

		$this->assertEquals(self::EXPECTED_ADDRESS, $result);	
	}


	function testAddressBuilderDirectorAddChiledException(): void
	{
		$this->expectException(\RuntimeException::class);
		$director = new AddressBuilderDirector($this->address, [self::FIRST_NAME, self::SECOND_NAME, self::THIRD_NAME], 1, 2);

		$director->addChiledAddr(new FakeQueryResult(self::SECOND_DATA))
				->addChiledAddr(new FakeQueryResult(self::FIRST_DATA))
				->addChiledAddr(new FakeQueryResult(self::FIRST_DATA))
				->addChiledAddr(new FakeQueryResult(self::FIRST_DATA));

	}
}
