<?php declare(strict_types=1);

namespace CLI;

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

	function wait(): void
	{
		if ($this->isParentTime()) {
			pcntl_waitpid($this->pid, $this->status);
		}
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

	function getPid(): int 
	{
		return $this->pid;
	}
}