<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Select;


use DB\Exceptions\Unchecked\BadQueryBuilderCallbackReturnExcpetion;
use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\ActiveRecord\ActiveRecord;
use RuntimeException;

class ImplSelect extends SelectQuery
{
	/**
	 * @param string $fields
	 * @param array<int|string, string> | array<string, callable(): ActiveRecord> | string $anotherTables
	 */
	function __construct(string $fields,
	                     array|string $anotherTables)
	{
		$dryArgsSum = [];

		if (is_array($anotherTables)) {
			/** @var array<int|string, string> $clearMappedAnotherTables */
			$clearMappedAnotherTables = [];

			/**
			 * @var  int|string $map
			 * @var  string|callable():ActiveRecord $tableName
			 */
			foreach ($anotherTables as $map => $tableName) {
				if (is_callable($tableName)) {
					['subQuery' => $subQuery, 'dryArgs' => $dryArgs] = $this->getSubQuery($map, $tableName);

					$tableNameStr = $subQuery;
					$dryArgsSum = array_merge($dryArgsSum, $dryArgs);

				} elseif(is_string($tableName)) {
					$tableNameStr = $tableName;

				} else {
					throw new RuntimeException("Incompatible anotherTables type given: " . print_r($anotherTables, true));

				}

				$clearMappedAnotherTables[$map] = $tableNameStr;
			}

			$anotherTables = DBFacade::mappedTableNamesToString($clearMappedAnotherTables);
		}



		parent::__construct(
			$this::createQueryBox(
				clearArgs: [$fields, $anotherTables],
				dryArgs: $dryArgsSum
			)
		);
	}

	/**
	 * @param mixed $map
	 * @param callable():ActiveRecord $subQuery
	 * @return array{subQuery: string, dryArgs: DatabaseContract[]}
	 */
	private function getSubQuery(mixed $map, callable $subQuery): array
	{
		if (is_string($map)) {
			$record = $subQuery();

			if (is_a($record, ActiveRecord::class)) {
				$queryBox = $record->getQueryBox();
			} else {
				throw new BadQueryBuilderCallbackReturnExcpetion($this);
			}

			return [
				'subQuery' => '(' . trim($queryBox->getQuerySnapshot()) . ')',
				'dryArgs' => $queryBox->getDryArgs()
			];
		} else {
			throw new RuntimeException("Require string-mapped callback():ActiveRecord, but given pseudonym is '" . gettype($map) . "'");
		}
	}

}