<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\ActiveRecord;

use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\Templates\SQL;
use PHPUnit\Util\Xml\ValidationResult;

/**
 * Query immutable box class
 *
 * @phpstan-import-type DatabaseContract from \DB\ORM\DBAdapter\DBAdapter
 */
class QueryBox
{
	/** @var string  */
	public readonly string $querySnapshot;
	/** @var array<int, DatabaseContract> */
	public readonly array $dryArgs;

	/**
	 * @param SQL $template
	 * @param string|int|array<string,int> $clearArgs
	 * @param mixed $dryArgs
	 * @param QueryBox|null $parentBox
	 */
	public function __construct(SQL       $template,
								array     $clearArgs = [],
	                            array     $dryArgs = [],
	                            ?QueryBox $parentBox = null)
	{
		$parentSnapshot = '';
		$parentDryArgs = [];

		if (null !== $parentBox) {
			$parentSnapshot = $parentBox->querySnapshot;
			$parentDryArgs = $parentBox->dryArgs;

		}

		$this->querySnapshot = $parentSnapshot . self::genPreparedQuery($template, $clearArgs);
		$this->dryArgs = array_merge($parentDryArgs, $dryArgs);;
	}


	/**
	 * @param SQL $template
	 * @param array<mixed> $clearArgs
	 * @return string
	 */
	private static function genPreparedQuery(SQL $template,
                                             array $clearArgs): string
	{
		$query = '';

		try{
			$query = vsprintf(
				$template->value,
				$clearArgs
			);
		}
		catch(\Throwable $error) {
			DBFacade::dumpException($template, $error->getMessage(), $clearArgs);
		}

		return $query . SQL::SEPARATOR->value;
	}
}