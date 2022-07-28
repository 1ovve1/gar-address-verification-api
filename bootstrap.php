<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$basePath = __DIR__ . '/';

// prepare and read data from /.env file
$dotenv = Dotenv::createImmutable($basePath);
if (!file_exists($basePath . '.env')) {
	echo "Error while read .env file" . PHP_EOL;
	exit(-1);
}
$dotenv->load();

//prepare config handler
if (!is_dir($basePath . 'config')) {
	echo 'Directory ' . $basePath . 'config was not found in the root of project' . PHP_EOL;
	exit(-1);
}

$_SERVER['CONFIG'] = function (string $filename) use ($basePath) {
	$path = $basePath . 'config/' . $filename . '.php';

	if (!file_exists($path)) {
		echo 'File ' . $path . ' was not found' . PHP_EOL;
		exit - 1;
	}

	return require($path);
};