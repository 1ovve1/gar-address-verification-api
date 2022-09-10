<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\ActiveRecord;

use DB\ORM\DBFacade;
use DB\ORM\QueryBuilder\Templates\SQL;
use Throwable;

/**
 * Query immutable box class
 */
class QueryBox
{
	/**
	 * @var string
	 */
	private string $querySnapshot;
	/**
	 * @var array<int, DatabaseContract>
	 */
	private array $dryArgs;

	/**
	 * @param SQL $template
	 * @param array<string|int> $clearArgs
	 * @param array<int, DatabaseContract> $dryArgs
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
			$parentSnapshot = $parentBox->getQuerySnapshot();
			$parentDryArgs = $parentBox->getDryArgs();
		}

		$this->querySnapshot = $parentSnapshot . self::genPreparedQuery($template, $clearArgs);
		$this->dryArgs = array_merge($parentDryArgs, $dryArgs);
	}


	/**
	 * @param SQL $template
	 * @param array<string|int> $clearArgs
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
		catch(Throwable $error) {
			DBFacade::dumpException($template, $error->getMessage(), $clearArgs);
		}

		return $query . SQL::SEPARATOR->value;
	}

	/**
	 * @return string
	 */
	public function getQuerySnapshot(): string
	{
		return $this->querySnapshot;
	}

	/**
	 * @return array<DatabaseContract>
	 */
	public function getDryArgs(): array
	{
		return $this->dryArgs;
	}
}