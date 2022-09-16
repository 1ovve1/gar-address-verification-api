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
	 * Remove default handler connection
	 * @param bool $autocatch
	 */
	public function __construct($autocatch = false)
	{
		parent::__construct($autocatch);
	}

	/**
	 * @inheritDoc
	 */
	protected function setup(Options $options)
	{
		$options->setHelp('GAR BD FIAS CLI tool');

		$options->registerCommand('upload', 'upload gar archive to database');
		$options->registerOption('migrate', 'upload with migration', 'm', false, 'upload');
		$options->registerOption('migrate-recreate', 'upload with recreate migration', null, false, 'upload');
		$options->registerOption('region', 'concrete region(s) for upload', 'r', 'region-list', 'upload');
		$options->registerOption('only-regions', 'upload only regions information', null, false, 'upload');
		$options->registerOption('only-single', 'upload only single indexes', null, false, 'upload');
		$options->registerOption('thread', 'upload only single indexes', 't', true, 'upload');

		$options->registerCommand('migrate', 'create actual database structure in your database');
		$options->registerOption('drop', 'delete tables', 'd', false, 'migrate');
		$options->registerOption('recreate', 'drop and create tables', 'r', false, 'migrate');
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
				$this->migrateProcedure($params);
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

		if (isset($params['thread'])) {
			$uploader = new UploadFactory((int)$params['thread']);
		} else {
			$uploader = new UploadFactory((int)$_ENV['PROCESS_COUNT']);
		}

		if (isset($params['region'])) {
			$regions = $this->convertInputRegionsToArray($params['region']);
		}

		if (isset($params['migrate-recreate'])) {
			$this->dropTablesAndMigrate();
		} elseif (isset($params['migrate'])) {
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
	 * @param array<string, string|bool> $params
	 * @return void
	 */
	function migrateProcedure(array $params = []): void
	{
		if (isset($params['recreate'])) {
			$this->dropTablesAndMigrate();
		} elseif (isset($params['drop'])) {
			$this->dropTables();
		} else {
			$this->migrate();
		}
	}

	function dropTables() : void
	{
		echo "Try to drop tables from config...\t";
		UserMigrations::dropTablesFromConfig();
		echo "DONE!" . PHP_EOL;
	}

	function migrate(): void
	{
		echo "Try migrate tables from config...\t";
		UserMigrations::migrateFromConfig();
		echo "DONE!" . PHP_EOL;
	}

	function dropTablesAndMigrate(): void
	{
		$this->dropTables();
		$this->migrate();
	}
}