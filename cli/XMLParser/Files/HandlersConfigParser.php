<?php

namespace CLI\XMLParser\Files;

use RuntimeException;

define('FILES_SPECIFICATION', [
	'root' => [
		'namespace',
		'handlers'
	],
	'regions' => [
		'namespace',
		'handlers'
	],
]);

define('CONFIG_NAME', 'xml_handlers_config');

class HandlersConfigParser
{
	/** @var String[][][] */
	private array $handlersConfig = [];

	public function __construct()
	{
		$config = self::validateAndParse($_SERVER['config'](CONFIG_NAME));

		if (!$config) {
			throw new RuntimeException(
				"Invalid config configuration in {$_ENV['CONFIG_PATH']}/" . CONFIG_NAME
			);
		}

		$this->handlersConfig = $config;
	}

	/**
	 * @param  mixed $config
	 * @return bool|String[][][] - false if validate error, config if correct
	 */
	static function validateAndParse(mixed $config): bool|array
	{
		$parsedConfig = [];

		if (!is_array($config)) {
			trigger_error(sprintf(
				"Require return array in config/%s",
				 CONFIG_NAME
			));
			return false;
		}
		foreach (FILES_SPECIFICATION as $handlerType => $handlerSpecification) {
			if (!key_exists($handlerType, $config)) {
				trigger_error(sprintf(
					"Require key %s in config/%s",
					$handlerType, CONFIG_NAME
				));
				return false;
			}
			if (!is_array($handlerSpecification)) {
				trigger_error(sprintf(
					"Key %s should contain array in config/%s (%s given)",
					$handlerType, CONFIG_NAME, gettype($handlerSpecification)
				));
				return false;
			}
			foreach ($handlerSpecification as $spec) {
				if (!key_exists($spec, $config[$handlerType])) {
					trigger_error(sprintf(
						"Require key %s => %s in config/%s",
						$handlerType, $spec, CONFIG_NAME
					));
					return false;
				}
				if (!is_array($config[$handlerType][$spec])) {
					trigger_error(sprintf(
							"Value by key %s => %s should be array type in config/%s (%s given)",
							$handlerType, $spec, CONFIG_NAME, gettype($config[$handlerType][$spec])
					));
					return false;
				}
				$parsedConfig[$handlerType][$spec] = $config[$handlerType][$spec];
			}
		}

		return $parsedConfig;
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
	 * @param String[][] $concreteHandlers
	 * @return array<int, XMLFile>
	 */
	private static function getClasses(array $concreteHandlers): array
	{
		$result = [];

		foreach ($concreteHandlers['namespace'] as $namespace) {
			foreach ($concreteHandlers['handlers'] as $handler) {
				$classNamespace = $namespace . $handler;

				if (class_exists($classNamespace)) {
					$object = new $classNamespace($handler);
					if ($object instanceof XMLFile) {
						$result[] = $object;
					} else {
						trigger_error(
							"Class {$classNamespace} not the instance of " . XMLFile::class
						);
					}
				} else {
					trigger_error(
						"Undefined class {$classNamespace}"
					);
				}
			}
		}

		return $result;
	}
}