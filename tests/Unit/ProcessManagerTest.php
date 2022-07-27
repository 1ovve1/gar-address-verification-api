<?php declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

use CLI\ProcessManager;


class ProcessManagerTest extends TestCase
{
	function testExitCodes(): void
	{
		$processCount = 3;

		$manager = new ProcessManager($processCount);

		for($i = 1; $i <= $processCount; ++$i)
		{
			$manager->newTask(
				function() use ($i) {
					sleep(2);
					exit($i);
				}
			);
		}

		$manager->waitAll();
		$result = $manager->getStatusHistory();
		
		$this->assertEquals($processCount, count($result));

		foreach ($result as $key => $process) {
			$this->assertEquals($key + 1, $process->getStatus());
		}
	}
}