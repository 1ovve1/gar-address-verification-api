<?php declare(strict_types=1);

namespace Tests\Unit;

defined('TEST_ENV') ?: define('TEST_ENV', __DIR__ . '/../.env.test');
require_once __DIR__ . '/../../bootstrap.php';

use CLI\XMLParser\Files\HandlersConfigParser;
use CLI\XMLParser\Files\XMLFile;
use PHPUnit\Framework\TestCase;

class HandlersConfigParserTest extends TestCase
{
	const TEST_CONFIG_CORRECT = [
		'root' => [
			'namespace' => [],
			'handlers' => [],
		],

		'regions' => [
			'namespace' => [],
			'handlers' => [],
		],
	];

	const TEST_CONFIG_DRY = [
		'boot',
		'root' => [
			'namespace' => [],
			'handlers' => [],
			'something else'
		],

		'hahahah',
		'regions' => [
			'namespace' => [],
			'handlers' => [],
			'something else' => '',
		],
	];

	function testParserValidation(): void
	{
		$result = HandlersConfigParser::validateAndParse(self::TEST_CONFIG_DRY);

		$this->assertIsNotBool($result);
		$this->assertEquals(self::TEST_CONFIG_CORRECT, $result);
	}

	function testBadParserValidation(): void
	{
		$config = self::TEST_CONFIG_DRY;
		unset($config['root']);

		$this->expectNotice();

		$result = HandlersConfigParser::validateAndParse($config);

		$this->assertIsBool($result);
	}

	function testBasicConfigRead(): void
	{
		$handlersConfigParser = new HandlersConfigParser();

		$regions = $handlersConfigParser->getHandlersClassesByRegions();

		$roots = $handlersConfigParser->getHandlersClassesByRoot();

		$this->assertNotEmpty($regions);
		$this->assertNotEmpty($roots);

		$this->assertContainsOnlyInstancesOf(XMLFile::class, $regions);
		$this->assertContainsOnlyInstancesOf(XMLFile::class, $roots);
	}
}