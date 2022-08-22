<?php declare(strict_types=1);

namespace CLI;

use CLI\Processes\ProcessManager;
use CLI\XMLParser\XMLParserClient;
use RuntimeException;

class UploadFactory
{
	/** @var ProcessManager */
	private readonly ProcessManager $manager;

	public function __construct()
	{
		$processCount = $_SERVER['PROCESS_COUNT'];
		if (!is_int($processCount) && !$processCount > 0) {
			throw new RuntimeException("Invalid count of PROCESS_COUNT in .env file ('{$processCount}')");
		}
		$this->manager = new ProcessManager((int)$processCount);
	}

	/**
	 * Upload full data from regions (single + regions)
	 * @param array<int, string>|null $regions
	 * @return void
	 */
	function upload(?array $regions = null): void
	{
		if (null === $regions) {
			$regions = $_SERVER['config']('regions');
		}

		$this->uploadSingle();

		$this->uploadRegions($regions);
	}

	/**
	 * Upload single indexes
	 * @return void
	 */
	function uploadSingle() : void
	{
		echo 'Upload single indexes tables...' . PHP_EOL;

		$this->manager->newTask(
			fn() => self::parseRegion(null, ['--onlySingle'])
		);
		$this->manager->waitAll();

		echo 'DONE!' . PHP_EOL;
	}

	/**
	 * Upload region information
	 * @param array<int, string> $regions
	 * @return void
	 */
	function uploadRegions(array $regions): void
	{
		echo 'Upload regions information...' . PHP_EOL;

		foreach ($regions as $region) {
			echo "{$region}..." . PHP_EOL;
			$this->manager->newTask(
				fn() => self::parseRegion([$region], ['--onlyRegions'])
			);
		}

		$this->manager->waitAll();
		echo 'DONE!' . PHP_EOL;
	}

	/**
	 *
	 * @param array<int, string>|null $region
	 * @param String[] $arguments
	 * @return void
	 */
	static function parseRegion(?array $region, array $arguments = []): void
	{
		(new XMLParserClient())->run($region, $arguments);
	}
}
