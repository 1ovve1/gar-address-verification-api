<?php declare(strict_types=1);

namespace Tests\Unit;

defined('TEST_ENV') ?: define('TEST_ENV', __DIR__ . '/../.env.test');
require_once __DIR__ . '/../../bootstrap.php';

use CLI\Exceptions\Unchecked\InvalidConfigParsingException;
use CLI\XMLParser\Files\HandlersConfigParser;
use CLI\XMLParser\Files\XMLFile;
use PHPUnit\Framework\TestCase;
use Tests\Mock\FakeXMLFile;


class HandlersConfigParserTest extends TestCase
{
	const CONFIG_CORRECT = [
		'root' => [
			FakeXMLFile::class
		],

		'regions' => [
			FakeXMLFile::class
		],
	];

	const CONFIG_WITH_UNKNOWN_FIELDS = [
		'boot' => [
			FakeXMLFile::class
		],
		'hahahah' => [
			FakeXMLFile::class
		],
	];

	const CONFIG_WITH_UNKNOWN_TYPES = [
		'root' => [
			FakeXMLFile::class
		],

		'regions' => [
			2132
		],
	];

	function testCorrect(): void
	{
		$config = HandlersConfigParser::validateAndParse(self::CONFIG_CORRECT);
		$this->assertEquals(self::CONFIG_CORRECT, $config);
	}

	function testUnknownFields(): void
	{
		$this->expectException(InvalidConfigParsingException::class);
		HandlersConfigParser::validateAndParse(self::CONFIG_WITH_UNKNOWN_TYPES);
	}

	function testUnknownTypes(): void
	{
		$this->expectException(InvalidConfigParsingException::class);
		HandlersConfigParser::validateAndParse(self::CONFIG_WITH_UNKNOWN_FIELDS);
	}
}