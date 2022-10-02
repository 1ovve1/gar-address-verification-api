<?php declare(strict_types=1);

namespace Tests\Mock;

use CLI\XMLParser\Files\XMLFile;

class FakeXMLFile extends XMLFile
{
	/**
	 * @return string
	 */
	public static function getElement(): string
	{
		return '';
	}

	/**
	 * @return String[]
	 */
	public static function getAttributes(): array
	{
		return ['sdfd'];
	}

	/**
	 * @return mixed
	 */
	public static function getTable(): mixed
	{
		return null;
	}

	/**
	 * @param array{} $values
	 * @param mixed $table
	 * @return void
	 */
	public function execDoWork(array $values, mixed $table): void
	{

	}

}