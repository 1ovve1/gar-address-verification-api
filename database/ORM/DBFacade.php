<?php declare(strict_types=1);

namespace DB\ORM;

use DB\Exceptions\Checked\ConditionNotFoundException;
use DB\Exceptions\Unchecked\DriverImplementationNotFoundException;
use DB\ORM\DBAdapter\DBAdapter;
use DB\ORM\DBAdapter\PDO\PDOObject;
use DB\ORM\Resolver\DBResolver;
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
     * @param  string $className - full class name namespace
     * @return string
     */
    public static function genTableNameByClassName(string $className): string
    {
	    $negStrLen = -strlen($className);
	    $tableName = '';

	    for ($index = -1, $char = $className[$index];
	         $index >= $negStrLen && $char !== '\\' && $char !== null;
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
				throw new RuntimeException('Incorrect tableName format (tableName should be a string - ' . gettype($tableName) . 'given');
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

		try {
			$sign = DBResolver::cond((string)$sign_or_value);
		} catch (ConditionNotFoundException) {
			try {
				$sign = DBResolver::cond_eq();
			} catch (ConditionNotFoundException $e) {
				throw new DriverImplementationNotFoundException($e->dbType, $e->getMessage(), $e);
			}
			$value = $sign_or_value;
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
	 * @return array{tableName: string, condition: array<int, string>} - pattern: ['tableMame', ['field1_with_or_without_pseudonym', 'field2_with_or_without_pseudonym']
	 */
	public static function joinArgsHandler(array|string $tableName, array $condition): array
	{
		if (is_array($tableName)) {
			$tableName = current($tableName) . DBResolver::fmtPseudoTables() . key($tableName);
		}
		$condition = match (count($condition)) {
			1 => (is_string(key($condition)))
				? [key($condition), current($condition)]
				: throw new RuntimeException('Condition count are incorrect (use [field1 => field2] or [field1, field2] notation]'),

			2 => self::convertFieldsWithPseudonym($condition),

			default => throw new RuntimeException('Condition count are incorrect (use [field1 => field2] or [field1, field2] notation]')
		};

		return ['tableName' => $tableName, 'condition' => $condition];
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
}
