<?php declare(strict_types=1);

namespace CLI\Processes;

class Process 
{
	/** @var int */
	private int $pid;
	/** @var null|int */
	private ?int $status = null;

	/**
	 * @param callable $task - callback
	 */
	function __construct(callable $task)
	{
		$pid = pcntl_fork();

		if ($pid == -1) {
			throw new \RuntimeException("Process fork error");
		}

		$this->pid = $pid;

		$this->run($task);
	}

	/**
	 * @param callable $task
	 * @return void
	 */
	private function run(callable $task): void
	{
		if (!$this->isParentScope()) {
			$task();
			exit(1);
		}
	}

	/**
	 * wait process by flag option
	 * @param int $flag
	 * @return void
	 */
	function wait(int $flag = 0): void
	{
		if ($this->isParentScope()) {
			$tryStatus = 0;
			pcntl_waitpid($this->pid, $tryStatus, $flag);
			if ($tryStatus) {
				$this->setStatus($tryStatus);
			}
		}
	}

	/**
	 * Update process status
	 * @return void
	 */
	function updateStatus(): void
	{
		$this->wait(WNOHANG);
	}

	/**
	 * @return int - status of process die
	 */
	static function waitUntilLastProcessComplete(): int
	{
		$status = 0;

		pcntl_wait($status);

		return $status;
	}

	/**
	 * Check if it is a parent scope
	 * @return bool
	 */
	private function isParentScope(): bool
	{
		return $this->pid > 0;
	}

	/**
	 * Return status of active process
	 * @return int|null
	 */
	function getStatus(): null|int 
	{
		return match ($this->status) {
			null => null,
			0 => 0,
			default => $this->status >> 8,
		};
	}

	/**
	 * set status by value
	 * @param int $value
	 * @return void
	 */
	private function setStatus(int $value): void
	{
		$this->status = $value;
	}

	/**
	 * return pid of process
	 * @return int
	 */
	function getPid(): int 
	{
		return $this->pid;
	}

}