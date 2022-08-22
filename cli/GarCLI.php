<?php declare(strict_types=1);

namespace CLI;

use splitbrain\phpcli\{
	CLI,
	Options
};
use DB\UserMigrations;

class GarCLI extends CLI
{
	/**
	 * @inheritDoc
	 */
	protected function setup(Options $options)
	{
		$options->setHelp('GAR BD FIAS CLI tool');

		$options->registerCommand('upload', 'upload gar archive to database');
		$options->registerOption('migrate', 'upload with migration', 'm', false, 'upload');
		$options->registerOption('region', 'concrete region(s) for upload', 'r', 'region-list', 'upload');
		$options->registerOption('only-regions', 'upload only regions information', null, false, 'upload');
		$options->registerOption('only-single', 'upload only single indexes', null, false, 'upload');

		$options->registerCommand('migrate', 'create actual database structure in your database');
	}

	/**
	 * @inheritDoc
	 */
	protected function main(Options $options)
	{
		$params = $options->getOpt();

		switch ($options->getCmd()) {
			case 'upload':
				$this->uploadProcedure($params);
				break;
			case 'migrate':
				$this->migrate();
				break;
		}
	}

	/**
	 * Handle params and upload
	 * @param array<string, string|bool> $params
	 * @return void
	 */
	function uploadProcedure(array $params): void
	{
		$regions = null;
		$uploader = new UploadFactory();

		if (isset($params['region'])) {
			$regions = $this->convertInputRegionsToArray($params['region']);
		}
		if (isset($params['migrate'])) {
			$this->migrate();
		}

		if (isset($params['only-regions'])) {
			$uploader->uploadRegions($regions);
		} elseif (isset($params['only-single'])) {
			$uploader->uploadSingle();
		} else {
			$uploader->upload($regions);
		}
	}

	/**
	 * Convert input in array form
	 * @param string $input - string input
	 * @return array<int, string>
	 */
	function convertInputRegionsToArray(string $input): array
	{
		$converted = [];
		$splitInput = explode(',', $input);

		foreach ($splitInput as $word) {
			$word = trim($word);

			if (is_numeric($word) && !(in_array($word, $converted))) {
				if ((int)$word >= 0 && (int)$word < 10) {
					$word = '0' . $word;
				}
				$converted[] = $word;
			}
		}

		return $converted;
	}

	/**
	 * Doing migrations
	 * @return void
	 */
	function migrate(): void
	{
		echo 'Try migrate tables from config...' . PHP_EOL;
		UserMigrations::doMigrateFromConfig();
		echo 'DONE!' . PHP_EOL;
	}
}