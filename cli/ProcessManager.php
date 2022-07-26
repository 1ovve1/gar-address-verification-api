<?php declare(strict_types=1);

namespace CLI;

pcntl_async_signals(true);

use CLI\Process;

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

	function newTask(callable $taskCallback): void
	{
		match ($this->isProcessBufferNotFull()) {
			true => $this->createProcessAndRun($taskCallback),
			false => $this->waitQueue(),
		};
	}

	function isProcessBufferNotFull(): bool 
	{
		return count($this->processBuffer) < $this->maxProcessCount;
	}

	private function createProcessAndRun(callable $task): void 
	{
		$this->processBuffer[] = new Process($task);
	}

	function waitQueue(): void
	{
		$firstProcess = $this->getFirstProcess();

		$firstProcess->wait();

		$this->history[] = $firstProcess;
		$this->shiftBuffer();
	}

	private function getFirstProcess(): Process
	{
		return $this->processBuffer[0];
	}

	private function shiftBuffer(): void
	{
		array_shift($this->processBuffer);
	}

	function waitAll(): void
	{
		foreach ($this->processBuffer as $ignored) {
			$this->waitQueue();
		}
	}

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

		$this->processBuffer = array_values($this->processBuffer);
	}
}