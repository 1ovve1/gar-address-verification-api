<?php declare(strict_types=1);

namespace DB\ORM\Resolver;

use DB\Exceptions\Checked\ConditionNotFoundException;
use DB\Exceptions\Unchecked\DriverImplementationNotFoundException;
use DB\Exceptions\Unchecked\UnknownDBDriverException;

class DBResolver
{
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
			'mysql' => $_SERVER['config']('drivers/mysql_driver'),
			'pgsql' => $_SERVER['config']('drivers/pgsql_driver'),
			default => throw new UnknownDBDriverException($dbType)
		};

		self::$dbType = $dbType;
		self::$dbSQL = $driver[AST::SQL] ??
			throw new UnknownDBDriverException($dbType, $driver, 'invalidContract');
		self::$dbConditions = $driver[AST::COND] ??
			throw new UnknownDBDriverException($dbType, $driver, 'invalidContract');
		self::$dbFmt = $driver[AST::FMT] ?? null;
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

	/**
	 * Check if condition exist in driver implementation
	 * @param string $dryCond
	 * @return string
	 * @throws ConditionNotFoundException - if condition not exist
	 */
	public static function cond(string $dryCond): string
	{
		$cond = strtoupper(trim($dryCond));

		return self::getDbConditions()[$cond] ??
			throw new ConditionNotFoundException(self::$dbType, $cond);
	}

	/**
	 * Return default condition
	 * @return string
	 * @throws ConditionNotFoundException
	 */
	public static function cond_eq(): string
	{
		return self::getDbConditions()[AST::COND_EQ] ??
			throw new ConditionNotFoundException(self::$dbType, AST::COND_EQ);
	}

	/**
	 * return separator for current driver (or default)
	 * @return string
	 */
	public static function fmtSep(): string
	{
		return self::getDbFmt()[AST::FMT_SEP] ?? AST::FMT_SEP;
	}

	/**
	 * return pseudonym separator for current driver (or default)
	 * @return string
	 */
	public static function fmtPseudoFields(): string
	{
		return self::getDbFmt()[AST::FMT_PS_FIELDS] ?? AST::FMT_PS_FIELDS;
	}

	/**
	 * also like a fmtPseudoFields but for tables
	 * @return string
	 */
	public static function fmtPseudoTables(): string
	{
		return self::getDbFmt()[AST::FMT_PS_TABLES] ?? AST::FMT_PS_TABLES;
	}
}