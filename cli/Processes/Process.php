<?php declare(strict_types=1);

namespace CLI\Processes;

class Process 
{
	/** @var int */
	private int $pid;
	/** @var null|int */
	private ?int $status = null;

	function __construct(callable $task)
	{
		$pid = pcntl_fork();

		if ($pid == -1) {
			throw new \RuntimeException("Process fork error");
		}

		$this->pid = $pid;

		$this->run($task);
	}

	private function run(callable $task): self
	{
		if (!$this->isParentTime()) {
			$task();
			exit(1);
		}

		return $this;
	}

	function wait(int $flag = 0): void
	{
		if ($this->isParentTime()) {
			$tryStatus = 0;
			pcntl_waitpid($this->pid, $tryStatus, $flag);
			if ($tryStatus) {
				$this->setStatus($tryStatus);
			}
		}
	}

	function updateStatus(): void
	{
		$this->wait(WNOHANG);
	}

	static function waitUntilLastProcessComplete(): int
	{
		$status = 0;

		pcntl_wait($status);

		return $status;
	}

	private function isParentTime(): bool 
	{
		return $this->pid > 0;
	}

	function getStatus(): null|int 
	{
		return match ($this->status) {
			null => null,
			0 => 0,
			default => $this->status >> 8,
		};
	}

	function setStatus(int $value): void
	{
		$this->status = $value;
	}

	function getPid(): int 
	{
		return $this->pid;
	}

}