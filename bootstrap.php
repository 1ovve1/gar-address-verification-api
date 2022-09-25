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

// load env
loadEnv($basePath, $envName);

// check cli context
if (!defined('SERVER_START')){
	require_once __DIR__ . '/cli/cli_bootstrap.php';
}


// some of func here

/**
 * env load procedure
 * @param string $basePath - path to the env file
 * @param string $envName - name of env file ('.env' by default)
 * @return void
 */
function loadEnv(string $basePath, string $envName = '.env'): void
{
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
}