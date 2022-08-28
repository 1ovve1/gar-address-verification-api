<?php declare(strict_types=1);

namespace Tests\Unit;

use GAR\Repository\Address\AddressBuilder;
use GAR\Repository\Address\AddressBuilderImplement;
use PHPUnit\Framework\TestCase;

class AddressBuilderImplementTest extends TestCase
{

	const FIRST_NAME = 'Калмыкия';
	const FIRST_DATA = [
		"name" => "Калмыкия",
		"typename" => "Респ",
		"objectid" => 110423
	];

	const SECOND_NAME = 'Лаганский';
	const SECOND_DATA = [
		"name" => "Лаганский",
		"typename" => "м.р-н",
		"objectid" => 95232638
	];

	const THIRD_NAME = 'Джалыковское';
	const THIRD_DATA = [
		"name" => "Джалыковское",
		"typename" => "с.п.",
		"objectid" => 95232643
	];

	const FOUR_VARIANT_DATA = [
		[
			"name" => "Джалыково",
			"typename" => "с",
			"objectid" => 114333
		], [
			"name" => "Буранное",
			"typename" => "с",
			"objectid" => 114417
		]
	];

	const HOUSES_DATA = [
		[
			'name' => 1,
			'objectid' => 2222
		], [
			'name' => 2,
			'objectid' => 4444
		]
	];

	const EXPECTED_ADDRESS = [
		0 => [
            self::FIRST_NAME => [
				self::FIRST_DATA
            ],
		],
		1 => [
			self::SECOND_NAME => [
				self::SECOND_DATA
			],
		],
        2 => [
	        self::THIRD_NAME => [
				self::THIRD_DATA
			]
		],
		3 => [
			"variant" =>
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


	public function testAddParentAddr()
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

	public function testAddChiledAddr()
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
}
