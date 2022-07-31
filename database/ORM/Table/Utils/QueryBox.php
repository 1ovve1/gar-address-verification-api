<?php declare(strict_types=1);

namespace DB\ORM\Table\Utils;

use DB\ORM\DBFacade;
use DB\ORM\Table\Templates\SQL;
use PHPUnit\Util\Xml\ValidationResult;

/**
 * Query immutable box class
 *
 * @phpstan-import-type DatabaseContract from \DB\ORM\DBAdapter\DBAdapter
 */
class QueryBox
{
	/** @var SQL[] */
	public readonly array $templates;
	/** @var array<int, string|int|array<string,int>> */
	public readonly array $clearArgs;
	/** @var array<int, array<mixed>> */
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
		if (null !== $parentBox) {
			$newTemplateList = $parentBox->templates;
			$newTemplateList[] = $template;

			$newClearArgs = $parentBox->clearArgs;
			$newClearArgs[] = $clearArgs;

			$newDryArgs = $parentBox->dryArgs;
			$newDryArgs[] = $dryArgs;

		} else {
			$newTemplateList = [$template];
			$newClearArgs = [$clearArgs];
			$newDryArgs = [$dryArgs];
		}

		$this->templates = $newTemplateList;
		$this->clearArgs = $newClearArgs;
		$this->dryArgs = $newDryArgs;
	}


	public function getPreparedQueryFromQueryBox(): string
	{
		$query = '';
		foreach ($this->templates as $index => $templateEnum) {
			$template = $templateEnum->value;
			$args = $this->clearArgs[$index];
			try{
				$partOfQuery = vsprintf($template, $args);
			}
			catch(\Throwable $error) {
				DBFacade::dumpException($this, $error->getMessage(), $args);
			}

			$query .= $partOfQuery . PHP_EOL;
		}

		return $query;
	}

	public function getDryArgumentsList(): array
	{
		$dryArgList = [];
		foreach ($this->dryArgs as $args) {
			$dryArgList = array_merge($dryArgList, $args);
		}

		return $dryArgList;
	}
}