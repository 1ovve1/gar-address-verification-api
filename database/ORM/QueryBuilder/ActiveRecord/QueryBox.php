<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\ActiveRecord;

use DB\ORM\Resolver\DBResolver;

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
	 * @param string $template
	 * @param array<string|int> $clearArgs
	 * @param array<int, DatabaseContract> $dryArgs
	 * @param QueryBox|null $parentBox
	 */
	public function __construct(string       $template,
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
	 * @param string $template
	 * @param array<string|int> $clearArgs
	 * @return string
	 */
	private static function genPreparedQuery(string $template,
                                             array $clearArgs): string
	{
		$query = vsprintf(
			$template,
			$clearArgs
		);

		return $query . DBResolver::fmtSep();
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