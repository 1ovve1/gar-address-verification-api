<?php declare(strict_types=1);

namespace CLI\Processes;

pcntl_async_signals(true);

class ProcessManager
{
	/** @var int **/
	private readonly int $maxProcessCount;
	/** @var Process[] */
	private array $processBuffer = [];
	/** @var Process[] */
	private array $history = [];

	function __construct(int $maxProcessCount)
	{
		if ($maxProcessCount <= 0) {
			throw new \RuntimeException("Incorect tasks count '{$maxProcessCount}'");
		} 

		$this->maxProcessCount = $maxProcessCount;
	}

	function newTask(callable $taskCallback, bool $queueMod = false): void
	{
		$this->updateInfoAboutActiveProcesses();

		if (!$this->isProcessBufferNotFull()) {
			match ($queueMod) {
				true    => $this->waitQueue(),
				false   => $this->waitAsync(),
			};
		}

		$this->createProcessAndRun($taskCallback);
	}

	function isProcessBufferNotFull(): bool 
	{
		return count($this->processBuffer) < $this->maxProcessCount;
	}

	private function createProcessAndRun(callable $task): void 
	{
		$this->processBuffer[] = new Process($task);
	}

	function waitAsync(): void
	{
		Process::waitUntilLastProcessComplete();		

		$this->updateInfoAboutActiveProcesses();
	}

	function waitQueue(): void
	{
		$shiftProcess = array_shift($this->processBuffer);
		$shiftProcess->wait();
		$this->history[] = $shiftProcess;
	}

	function waitAll(): void
	{
		foreach ($this->processBuffer as $process) {
			$process->wait();
		}
	}

	/**
	 * @return Process[]
	 */
	function getStatusHistory(): array
	{
		$this->updateInfoAboutActiveProcesses();
		return $this->history;
	}

	private function updateInfoAboutActiveProcesses(): void
	{
		foreach ($this->processBuffer as $key => $process) {
			if (null !== $process->getStatus()) {
				$this->history[] = $process;
				unset($this->processBuffer[$key]);
			}
		}
	}
}