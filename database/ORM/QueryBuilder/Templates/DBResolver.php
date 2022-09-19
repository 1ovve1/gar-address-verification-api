<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\Templates;

use DB\Exceptions\Unchecked\DriverImplementationNotFoundException;
use DB\Exceptions\Unchecked\UnknownDBDriverException;

class DBResolver
{
	const CONDITIONS = 'conditions';
	const SQL = 'sql';
	const FMT = 'fmt';

	const SEPARATOR = ' ';
	const PSEUDONYMS_FIELDS = '.';
	const PSEUDONYMS_TABLES = ' as ';

	/** @var string */
	static private string $dbType;
	/** @var array<string, string> */
	static private array $dbSQL;
	/** @var array<string, string> */
	static private array $dbConditions;
	/** @var null|array<string, string> */
	static private ?array $dbFmt;

	static private function init(): void
	{
		$dbType = $_ENV["DB_TYPE"] ?? null;

		$driver = match($dbType) {
			'mysql' => require __DIR__ . '/MySQL/mysql_driver.php',
			'pgsql' => require __DIR__ . '/PgSQL/pgsql_driver.php',
			default => throw new UnknownDBDriverException($dbType)
		};

		self::$dbType = $dbType;
		self::$dbSQL = $driver[self::SQL] ??
			throw new UnknownDBDriverException($dbType, $driver, 'invalidContract');
		self::$dbConditions = $driver[self::CONDITIONS] ??
			throw new UnknownDBDriverException($dbType, $driver, 'invalidContract');
		self::$dbFmt = $driver[self::FMT] ?? null;
	}

	/**
	 * @return array<string, string>
	 */
	public static function getDbSQL(): array
	{
		if (!isset(self::$dbSQL)) {
			self::init();
		}
		return self::$dbSQL;
	}

	/**
	 * @return array<string, string>
	 */
	public static function getDbConditions(): array
	{
		if (!isset(self::$dbConditions)) {
			self::init();
		}
		return self::$dbConditions;
	}

	/**
	 * @return array<string, string>|null
	 */
	public static function getDbFmt(): ?array
	{
		if (!isset(self::$dbFmt)) {
			self::init();
		}
		return self::$dbFmt;
	}

	/**
	 * return sql string for these implementation name
	 * @param string $implName
	 * @return string
	 */
	public static function sql(string $implName): string
	{
		return self::getDbSQL()[$implName] ??
			throw new DriverImplementationNotFoundException(self::$dbType, $implName);
	}

	public static function cond(string $dryCond): string
	{
		$cond = strtoupper(trim($dryCond));

		return self::getDbConditions()[$cond] ??
			throw new DriverImplementationNotFoundException(self::$dbType, $cond);
	}

	/**
	 * return separator for current driver (or default)
	 * @return string
	 */
	public static function fmtSep(): string
	{
		return self::getDbFmt()[self::SEPARATOR] ?? self::SEPARATOR;
	}

	/**
	 * return pseudonym separator for current driver (or default)
	 * @return string
	 */
	public static function fmtPseudoFields(): string
	{
		return self::getDbFmt()[self::PSEUDONYMS_FIELDS] ?? self::PSEUDONYMS_FIELDS;
	}

	/**
	 * also like a fmtPseudoFields but for tables
	 * @return string
	 */
	public static function fmtPseudoTables(): string
	{
		return self::getDbFmt()[self::PSEUDONYMS_TABLES] ?? self::PSEUDONYMS_TABLES;
	}
}