<?php declare(strict_types=1);

namespace DB\ORM;

use DB\ORM\DBAdapter\DBAdapter;
use DB\ORM\DBAdapter\PDO\PDOObject;
use DB\ORM\QueryBuilder\Templates\Conditions;
use \RuntimeException;
use InvalidArgumentException;
use PDOException;

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
     * Connection via PDO
     *
     * @return PDOObject
     */
    public static function connectViaPDO(): PDOObject
    {
	    return new PDOObject(
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
		$strCollection = [];

		foreach ($fieldsWithPseudonyms as $pseudonym => $fields) {
			$strBuffer = '';

			if (is_array($fields)) {
				if (is_string($pseudonym)) {
					foreach ($fields as $f) {
						$strBuffer .= "{$pseudonym}.{$f}, ";
					}
					$strBuffer = substr($strBuffer, 0, -2);
				} else {
					foreach ($fields as $f) {
						$strBuffer .= "{$f}, ";
					}
				}

			} else {
				if (is_string($pseudonym)) {
					$strBuffer = "{$pseudonym}.{$fields}";
				} else {
					$strBuffer = "{$fields}";
				}
			}

			$strCollection[] = $strBuffer;
		}

		return implode(', ', $strCollection);
	}

	public static function tableNamesWithPseudonymsToString(array $tableNamesWithPseudonyms): string
	{
		$strBuffer = '';
		foreach ($tableNamesWithPseudonyms as $pseudonym => $tableName) {

			if (is_string($tableName)) {
				if (is_string($pseudonym)) {
					$strBuffer .= "{$tableName} as {$pseudonym}, ";

				} else {
					$strBuffer .= "{$tableName}, ";
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

	public static function whereArgsHandler(array|string                $field,
	                                        int|float|bool|string|null  $sign_or_value = '',
	                                        float|int|bool|string|null  $value = null) : array
	{
		if (is_array($field)) {
			if (count($field) === 1) {
				$field = DBFacade::fieldsWithPseudonymsToString($field);
			} else {
				DBFacade::dumpException(null, 'Count of elements in where statement should be 1', func_get_args());
			}
		}

		// now we try to make our 'where' by different params
		if (null === $value) {
			$sign = Conditions::EQ->value;
			$value = $sign_or_value;

		} else if(Conditions::tryFind($sign_or_value)) {
			$sign = Conditions::tryFind($sign_or_value);

		} else {
			DBFacade::dumpException(null, 'Incorrect params', func_get_args());
		}

		return [$field, $sign, $value];
	}

	/**
	 * @param array|string $tableName
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
			$tableName = current($tableName) . ' as ' . key($tableName);
		}
		$condition = match (count($condition)) {
			1 => [key($condition), current($condition)],
			2 => self::convertConditionWithPseudonym($condition),
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
	 * @return array
	 */
	private static function convertConditionWithPseudonym(array $conditionWithPseudonym) : array
	{
		$converted = [];
		foreach ($conditionWithPseudonym as $pseudonym => $field) {

			if (is_string($pseudonym)) {
				$converted[] = $pseudonym . '.' . $field;
			} else {
				$converted[] = $field;
			}
		}

		return $converted;
	}

	public static function dumpException(mixed $item, string $message, array $params): void
	{
		echo 'Dump of current item...' . PHP_EOL;
		echo '<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<' . PHP_EOL;
		var_dump($item);
		echo 'Params:' . PHP_EOL;
		var_dump($params);
		echo '>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>' . PHP_EOL;

		throw new RuntimeException($message);
	}
}
