<?php

//prepare config getter
if (!is_dir($_ENV['CONFIG_PATH'])) {
	throw new \RuntimeException('Directory ' . $_ENV['CONFIG_PATH'] . ' was not found in the root of project' . PHP_EOL);
}

$_SERVER['config'] = function (string $filename) {
	$path = $_ENV['CONFIG_PATH'] . "/{$filename}.php";
	if (!file_exists($path)) {
		throw new \RuntimeException('File ' . $path . ' was not found' . PHP_EOL);
	}

	return require($path);
};