<?php declare(strict_types=1);

namespace DB\ORM;

use DB\ORM\DBAdapter\DBAdapter;
use DB\ORM\DBAdapter\PDO\PDOObject;
use DB\ORM\QueryBuilder\Templates\DBResolver;
use DB\ORM\QueryBuilder\Templates\MySQL\Conditions;
use RuntimeException;

/**
 * Database facade static class
 */
class DBFacade
{
    /**
     * @var DBAdapter|null - database static object
     */
    public static ?DBAdapter $instance = null;

	/**
	 * Get curr instance of database
	 *
	 * @return DBAdapter
	 */
    public static function getDBInstance(): DBAdapter
    {
        if (self::$instance === null) {
	        self::$instance = self::connectViaPDO();

        }

        return self::$instance;
    }

	/**
	 * Connection without singleton
	 * @return DBAdapter
	 */
	public static function getImmutableDBConnection(): DBAdapter
	{
		return self::connectViaPDO();
	}

	/**
	 * Connection via PDO
	 *
	 * @return DBAdapter
	 */
    public static function connectViaPDO(): DBAdapter
    {
	    return PDOObject::connectViaDSN(
		    $_ENV['DB_TYPE'], $_ENV['DB_HOST'],
		    $_ENV['DB_NAME'], $_ENV['DB_PORT'],
		    $_ENV['DB_USER'], $_ENV['DB_PASS']
	    );
    }

    /**
     * Generate table name in snake_case
     * @param  class-string $className - full class name namespace
     * @return string
     */
    public static function genTableNameByClassName(string $className): string
    {
	    $negStrLen = -strlen($className);
	    $tableName = '';

	    for ($index = -1, $char = $className[$index];
	         $index >= $negStrLen && $char !== '\\';
	         --$index, $char = $className[$index] ?? null) {

		    if (ctype_upper($char)) {
			    $tableName = '_' . strtolower($char) . $tableName;
		    } else {
			    $tableName = $char . $tableName;
		    }

	    }

	    return substr($tableName, 1);
    }

	/**
	 * Generate vars for prepared statement (in PDO: '?')
	 * Return example: (?, ..., ?) ... (?, ... , ?)
	 *
	 * @return string - string of vars
	 */
	public static function genInsertVars(int $countOfFields, int $countOfGroups): string
	{
		$vars = sprintf(
			"(%s),",
			substr(str_repeat("?, ", $countOfFields), 0, -2)
		);

		$groupedVars = str_repeat($vars, $countOfGroups);

		return substr($groupedVars, 0, -1);
	}

	/**
	 * @param array<int|string, string|String[]> $fieldsWithPseudonyms
	 * @return string
	 */
	public static function fieldsWithPseudonymsToString(array $fieldsWithPseudonyms): string
	{
		$strResult = '';

		foreach ($fieldsWithPseudonyms as $pseudonym => $fields) {
			$strBuffer = '';

			if (is_array($fields)) {
				if (is_string($pseudonym)) {
					foreach ($fields as $f) {
						$strBuffer .= $pseudonym . DBResolver::fmtPseudoFields() . $f . ", ";
					}
					$strBuffer = substr($strBuffer, 0, -2);
				} else {
					foreach ($fields as $f) {
						$strBuffer .= "{$f}, ";
					}
				}

			} else {
				if (is_string($pseudonym)) {
					$strBuffer = $pseudonym . DBResolver::fmtPseudoFields() . $fields;
				} else {
					$strBuffer = $fields;
				}
			}

			$strResult .= $strBuffer . ", ";
		}

		return substr($strResult, 0, -2);
	}

	/**
	 * @param array<int|string, int|string> $tableNamesWithPseudonyms
	 * @return string
	 */
	public static function tableNamesWithPseudonymsToString(array $tableNamesWithPseudonyms): string
	{
		$strBuffer = '';
		foreach ($tableNamesWithPseudonyms as $pseudonym => $tableName) {

			if (is_string($tableName)) {
				if (is_string($pseudonym)) {
					$strBuffer .= $tableName . DBResolver::fmtPseudoTables() . $pseudonym . ", ";

				} else {
					$strBuffer .= $tableName . ", ";
				}
			} else {
				DBFacade::dumpException(
					null,
					'Incorrect tableName format (tableName should be a string - ' . gettype($tableName) . 'given',
					func_get_args()
				);
			}
		}

		return substr($strBuffer, 0, -2);
	}

	/**
	 * @param array<int|string, string>|string $field
	 * @param DatabaseContract $sign_or_value
	 * @param DatabaseContract $value
	 * @return array{field: string, sign: string, value: DatabaseContract}
	 */
	public static function whereArgsHandler(array|string                $field,
	                                        int|float|bool|string|null  $sign_or_value = '',
	                                        float|int|bool|string|null  $value = null) : array
	{
		if (is_array($field)) {
			if (count($field) === 1) {
				$field = DBFacade::fieldsWithPseudonymsToString($field);
			} else {
				throw new RuntimeException('You can use WHERE state only with single field element');
			}
		}

		// now we try to make our 'where' by different params
		if (null === $value) {
			if (is_string($sign_or_value) && Conditions::tryFrom($sign_or_value)) {
				$sign = $sign_or_value;
				$value = null;
			} else {
				$sign = DBResolver::cond('=');
				$value = $sign_or_value;
			}
		} else {
			$sign = DBResolver::cond((string)$sign_or_value);
		}

		return ['field' => $field, 'sign' => $sign, 'value' => $value];
	}

	/**
	 * @param array<string, string>|string $tableName
	 * @param array<string|int, string> $condition - support:
	 * 1. Pseudonym notation
	 *      [
	 *          'pseudonym1' => 'field1',
	 *          'pseudonym2' => 'field2',
	 *      ]
	 * 2. Just-fields notation
	 *      [ 'field1', 'field2' ]
	 * 3. Just-fields notation with assoc
	 *      [ 'field1' => 'field2' ]
	 * @return array<int, string|array<int,string>> - pattern: ['tableMame', ['field1_with_or_without_pseudonym', 'field2_with_or_without_pseudonym']
	 */
	public static function joinArgsHandler(array|string $tableName, array $condition): array
	{
		if (is_array($tableName)) {
			$tableName = current($tableName) . DBResolver::fmtPseudoTables() . key($tableName);
		}
		$condition = match (count($condition)) {
			1 => [key($condition), current($condition)],
			2 => self::convertFieldsWithPseudonym($condition),
			default => DBFacade::dumpException(
				null,
				'Condition count are incorrect (use [field1 => field2] or [field1, field2] notation]',
				func_get_args()
			)
		};

		return [$tableName, $condition];
	}

	/**
	 * See DBFacade::joinArgsHandler
	 * @param array<string|int, string> $conditionWithPseudonym
	 * @return array<String> - return format: [field1, field2]
	 */
	private static function convertFieldsWithPseudonym(array $conditionWithPseudonym) : array
	{
		$converted = [];
		foreach ($conditionWithPseudonym as $pseudonym => $field) {

			if (is_string($pseudonym)) {
				$converted[] = $pseudonym . DBResolver::fmtPseudoFields() . $field;
			} else {
				$converted[] = $field;
			}
		}

		return $converted;
	}

	/**
	 * Dump exception
	 * @param mixed $item
	 * @param string $message
	 * @param array<int, mixed> $params
	 * @return void
	 */
	public static function dumpException(mixed $item, string $message, array $params): void
	{
		if (!defined('SERVER_START')) {
			echo 'Dump of current item...' . PHP_EOL;
			echo '<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' . PHP_EOL;
			var_dump($item);
			echo 'Params:' . PHP_EOL;
			var_dump($params);
			echo '>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>' . PHP_EOL;
		}

		throw new RuntimeException($message);
	}
}
