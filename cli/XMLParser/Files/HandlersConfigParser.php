<?php

namespace CLI\XMLParser\Files;

use CLI\Exceptions\Unchecked\InvalidConfigParsingException;
use RuntimeException;

define('FILES_SPECIFICATION', [
	'root' => [
	],
	'regions' => [
	],
]);

define('CONFIG_NAME', 'xml_handlers_config');

class HandlersConfigParser
{
	/** @var String[][] */
	private array $handlersConfig = [];

	/**
	 * @param array{root: mixed, regions: mixed} $configPath
	 */
	public function __construct(?array $configPath = null)
	{
		$this->handlersConfig = self::validateAndParse($configPath ?? $_SERVER['config'](CONFIG_NAME));
	}

	/**
	 * @param  array{root: mixed, regions: mixed} $config
	 * @return String[][] - config
	 */
	static function validateAndParse(mixed $config): array
	{
		$copy = [];

		if (!is_array($config)) {
			throw new InvalidConfigParsingException(
				"Config should have a array type"
			);
		}

		foreach (FILES_SPECIFICATION as $handlerType => $handlers) {
			if (!key_exists($handlerType, $config)) {
				throw new InvalidConfigParsingException(sprintf(
					"Require key %s in config/%s",
					$handlerType, CONFIG_NAME
				));
			}
			if (is_array($config[$handlerType])) {
				foreach ($config[$handlerType] as $handler) {
					if (!is_string($handler)) {
						throw new InvalidConfigParsingException(
							"handler should be a string name of concrete class, ''" . gettype($handler) . "' given"
						);
					}
					$copy[$handlerType][] = $handler;
				}
			}
		}

		return $copy;
	}

	/**
	 * @return array<int, XMLFile>
	 */
	function getHandlersClassesByRoot() : array
	{
		return self::getClasses($this->handlersConfig['root']);

	}

	/**
	 * @return array<int, XMLFile>
	 */
	function getHandlersClassesByRegions() : array
	{
		return self::getClasses($this->handlersConfig['regions']);
	}

	/**
	 * @param String[] $classes
	 * @return XMLFile[]
	 */
	static private function getClasses(array $classes): array
	{
		$collection = [];

		foreach ($classes as $className) {
			if (!class_exists($className)) {
				throw new InvalidConfigParsingException(
					"'{$className}' should have a string class name with namespace"
				);
			}
			if (!is_a($className, XMLFile::class, true)) {
				throw new InvalidConfigParsingException(
					"'{$className}' should be an instance of " . XMLFile::class
				);
			}
			$collection[] = new $className();
		}

		return $collection;
	}
}