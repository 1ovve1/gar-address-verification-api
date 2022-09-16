<?php declare(strict_types=1);

namespace CLI\Processes;

pcntl_async_signals(true);

class ProcessManager
{
	/** @var int **/
	public readonly int $maxProcessCount;
	/** @var Process[] */
	private array $processBuffer = [];
	/** @var Process[] */
	private array $history = [];

	/**
	 * @param int $maxProcessCount
	 */
	function __construct(int $maxProcessCount)
	{
		if ($maxProcessCount < 0) {
			throw new \RuntimeException("Incorrect tasks count '{$maxProcessCount}'");
		} 

		$this->maxProcessCount = $maxProcessCount;
	}

	/**
	 * Create task and handle it
	 * @param callable $taskCallback
	 * @param bool $queueMod
	 * @return void
	 */
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

	/**
	 * Check if process buffer is not full
	 * @return bool
	 */
	function isProcessBufferNotFull(): bool 
	{
		return count($this->processBuffer) < $this->maxProcessCount;
	}

	/**
	 * Accept task and run process
	 * @param callable $task
	 * @return void
	 */
	private function createProcessAndRun(callable $task): void 
	{
		$this->processBuffer[] = new Process($task);
	}

	/**
	 * Wait by async model (until any of active process die)
	 * @return void
	 */
	function waitAsync(): void
	{
		Process::waitUntilLastProcessComplete();		

		$this->updateInfoAboutActiveProcesses();
	}

	/**
	 * Wait by FILO
	 * @return void
	 */
	function waitQueue(): void
	{
		$shiftProcess = array_shift($this->processBuffer);
		$shiftProcess->wait();
		$this->history[] = $shiftProcess;
	}

	/**
	 * Wait until all processes will be died
	 * @return void
	 */
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

	/**
	 * @return void
	 */
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