<?php declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// check phpunit context
if (!defined('PHPUNIT_TEST_RUNTIME')) {
	// load env
	$envName = '.env';
	$basePath = __DIR__ . '/';

	// add error handler
	require_once __DIR__ . '/default_handler_and_logger.php';
} else {
	// load env.test
	$envName = '.env.test';
	$basePath = __DIR__ . '/tests/';
}

// prepare and read data from /.env file
$dotenv = Dotenv::createImmutable($basePath, $envName);
try{
	$dotenv->load();
} catch (\Dotenv\Exception\InvalidPathException $e) {
	throw new RuntimeException("Name: '{$envName}'\nPath: '{$basePath}'\nMessage: '{$e->getMessage()}'", $e->getCode(), $e);
}

// update some env values with __DIR__ prefix
foreach ($_ENV as $index => &$param) {
	if (preg_match('/^.*_PATH$/', $index)) {
		$param = __DIR__ . $param;
	}
}

// check config flooder existing
if (!is_dir($_ENV['CONFIG_PATH'])) {
	throw new \RuntimeException('Directory ' . $_ENV['CONFIG_PATH'] . ' was not found in the root of project' . PHP_EOL);
}

// and add flooder-loader to the server variable
$_SERVER['config'] = function (string $filename) {
	$path = $_ENV['CONFIG_PATH'] . "/{$filename}.php";
	if (!file_exists($path)) {
		throw new \RuntimeException('File ' . $path . ' was not found' . PHP_EOL);
	}

	return require($path);
};
