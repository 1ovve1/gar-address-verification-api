<?php declare(strict_types=1);

namespace CLI;

use GAR\Migration\UserMigrations;
use splitbrain\phpcli\{CLI, Options};

class GarCLI extends CLI
{
	const UPLOAD = 'upload';
	const MIGRATE = 'migrate';
	const SERVE = 'serve';

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

		$options->registerCommand(self::UPLOAD, 'upload gar archive to database');
		$options->registerOption('migrate', 'upload with migration', 'm', false, self::UPLOAD);
		$options->registerOption('migrate-recreate', 'upload with recreate migration', null, false, self::UPLOAD);
		$options->registerOption('region', 'concrete region(s) for upload', 'r', 'region-list', self::UPLOAD);
		$options->registerOption('only-regions', 'upload only regions information', null, false, self::UPLOAD);
		$options->registerOption('only-single', 'upload only single indexes', null, false, self::UPLOAD);
		$options->registerOption('thread', 'upload only single indexes', 't', true, self::UPLOAD);

		$options->registerCommand(self::MIGRATE, 'create actual database structure in your database');
		$options->registerOption('drop', 'delete tables', 'd', false, self::MIGRATE);
		$options->registerOption('recreate', 'drop and create tables', 'r', false, self::MIGRATE);

		$options->registerCommand(self::SERVE, 'start server using special flag or .env conf');
	}

	/**
	 * @inheritDoc
	 */
	protected function main(Options $options)
	{
		/** @var array<string> $params */
		$params = $options->getOpt();

		switch ($options->getCmd()) {
			case self::UPLOAD:
				$this->uploadProcedure($params);
				break;
			case self::MIGRATE:
				$this->migrateProcedure($params);
				break;
			case self::SERVE:
				$this->startServer();
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
		$uploader = match(isset($params['thread'])) {
			true => new UploadFactory((int)$params['thread']),
			default => new UploadFactory((int)$_ENV['PROCESS_COUNT'])
		};
		$regions = match(isset($params['region'])) {
			true => $this->convertInputRegionsToArray((string)$params['region']),
			default => $_SERVER['config']('regions')
		};

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

	/**
	 * @return void
	 */
	function startServer(): void
	{
		$link = $_ENV['SERVER_HOST'] . ':' . $_ENV['SERVER_PORT'];

		echo "Trying to start server at '{$link}' ..." . PHP_EOL;

		if (filter_var($_ENV['SERVER_SWOOLE_ENABLE'], FILTER_VALIDATE_BOOL)) {
			shell_exec('php public/index.php');
		} else {
			shell_exec('cd public/ && php -S ' . $link);
		}

	}
}