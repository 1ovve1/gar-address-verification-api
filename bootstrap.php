<?php

require __DIR__ . '/vendor/autoload.php';

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

//prepare config handler
if (!is_dir(__DIR__ . '/config')) {
	throw new \RuntimeException('Directory ' . __DIR__ . '/config was not found in the root of project' . PHP_EOL);
}

$_SERVER['CONFIG'] = function (string $filename) use ($basePath) {
	$path = __DIR__ . '/config/' . $filename . '.php';

	if (!file_exists($path)) {
		throw new \RuntimeException('File ' . $path . ' was not found' . PHP_EOL);
	}

	return require($path);
};