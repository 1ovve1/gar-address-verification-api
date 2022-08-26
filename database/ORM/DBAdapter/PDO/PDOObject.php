<?php

declare(strict_types=1);

namespace DB\ORM\DBAdapter\PDO;

use DB\ORM\DBAdapter\{DBAdapter, QueryResult, QueryTemplate};
use DB\ORM\Migration\Container\Query;
use PDO;
use PDOException;
use RuntimeException;

/**
 * Implements DBAdapter for PDO connection type
 *
 * @phpstan-import-type DatabaseContract from DBAdapter
 */
class PDOObject implements DBAdapter
{
    /** @var PDO - curr instance of db connection */
    private readonly PDO $instance;

    /**
     * @param string $dbType - type name of curr db
     * @param string $dbHost - db host
     * @param string $dbName - db name
     * @param string $dbPort - port
     */
    public function __construct(string $dbType, string $dbHost,
                                string $dbName, string $dbPort,
                                string $dbUsername, string $dbPass
    ) {
	    $dsn = self::getDsn($dbType, $dbHost, $dbName, $dbPort);
		$this->instance = self::connectPDO($dsn, $dbUsername, $dbPass);
    }

	/**
	 * Generate DSN by params
	 * @param string $dbType
	 * @param string $dbHost
	 * @param string $dbName
	 * @param string $dbPort
	 * @return string
	 */
	protected static function getDsn(string $dbType, string $dbHost,
	                                 string $dbName, string $dbPort) : string
	{
		return sprintf(
			'%s:host=%s;dbname=%s;port=%s;charset=utf8',
			$dbType, $dbHost, $dbName, $dbPort,
		);
	}

	/**
	 * Create connection to PDO
	 *
	 * @param string $dsn - dsn string format
	 * @param string $dbUsername
	 * @param string $dbPass
	 * @return PDO - connection to db
	 */
	protected static function connectPDO(string $dsn,
	                                     string $dbUsername,
	                                     string $dbPass) : PDO
	{
		return new PDO(
			$dsn, $dbUsername, $dbPass,
			[
				PDO::ATTR_PERSISTENT => true,
				PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
			]
		);
	}

	/**
	 * {@inheritDoc}
	 */
    public function rawQuery(Query $query): QueryResult
    {
		try {
			$res = $this->instance->query($query->getRawSql());
		} catch (PDOException $e) {
			throw new RuntimeException(sprintf(
				"Bad query request: '%s' " . PHP_EOL . "Message: %s" . PHP_EOL . PHP_EOL . 'Traceback: %s',
				$query->getRawSql(), $e->getMessage(), $e->getTraceAsString()
			));
		}

		if (false === $res) {
			throw new RuntimeException('Bad query request: ' . $query->getRawSql());
		}

        return new PDOQueryResult($res);
    }

	/**
	 * {@inheritDoc}
	 */
    public function prepare(string $template): QueryTemplate
    {
        $template = $this->instance->prepare($template);

        return new PDOTemplate($template);
    }

	/**
	 * {@inheritDoc}
	 */
    public function getForceInsertTemplate(string $tableName,
                                           array $fields,
                                           int $stagesCount = 1): QueryTemplate
    {
        return new PDOForceInsertTemplate($this, $tableName, $fields, $stagesCount);
    }
}
