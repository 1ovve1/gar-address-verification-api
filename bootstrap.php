<?php declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;


if (defined('TEST_ENV')) {
	$envName = '.env.test';
	$basePath = str_replace('.env.test', '', TEST_ENV);
} else {
	$envName = '.env';
	$basePath = __DIR__ . '/';
}

// prepare and read data from /.env file
$dotenv = Dotenv::createImmutable($basePath, $envName);
if (!file_exists($basePath . $envName)) {
	echo "Error while read '{$envName}' file in '{$basePath}'" . PHP_EOL;
	exit(-1);
}
$dotenv->load();

// update some env values with __DIR__ prefix

foreach ($_ENV as $index => &$param) {
	if (is_string($param)) {
		if (preg_match('/^.*_PATH$/', $index)) {
			$param = __DIR__ . $param;
		}
	}
}

// add error handler

if (!defined('PHPUNIT_TEST_RUNTIME')) {
	require_once __DIR__ . '/default_handler_and_logger.php';
}

// check context (cli or web)
if (!defined('SERVER_START')){
	require_once __DIR__ . '/cli/cli_bootstrap.php';
}
