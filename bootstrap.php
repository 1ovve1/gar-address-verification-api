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

// check context (cli or web)
if (!key_exists('argv', $_SERVER)){
	return;
}

// prepare and read data from /.env file
$dotenv = Dotenv::createImmutable($basePath, $envName);
if (!file_exists($basePath . $envName)) {
	echo "Error while read '{$envName}' file in '{$basePath}'" . PHP_EOL;
	exit(-1);
}
$dotenv->load();



// update some env values with __DIR__ prefix

$_ENV['CACHE_PATH'] = __DIR__ . $_ENV['CACHE_PATH'];
$_ENV['CONFIG_PATH'] = __DIR__ . $_ENV['CONFIG_PATH'];
$_ENV['ARCHIVE_PATH'] = __DIR__ . $_ENV['ARCHIVE_PATH'];


//prepare config getter
if (!is_dir($_ENV['CONFIG_PATH'])) {
	throw new \RuntimeException('Directory ' . getenv('CONFIG_PATH') . '/config was not found in the root of project' . PHP_EOL);
}

$_SERVER['config'] = function (string $filename) {
	$path = $_ENV['CONFIG_PATH'] . "/{$filename}.php";
	if (!file_exists($path)) {
		throw new \RuntimeException('File ' . $path . ' was not found' . PHP_EOL);
	}

	return require($path);
};