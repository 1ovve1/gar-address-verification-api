<?php declare(strict_types=1);

namespace CLI\Exceptions\Unchecked;

use RuntimeException;
use XMLReader;

class EmptyDataException extends RuntimeException
{
	/**
	 * @param XMLReader $readerFocusedOnCurrElem
	 * @param String[] $attributesList
	 */
	public function __construct(XMLReader &$readerFocusedOnCurrElem, array $attributesList = [])
	{
		$message = "WARNING: reader not found attributes: '"
			. implode(', ', $attributesList)
			. "'" . PHP_EOL
			. "XML String: " . $readerFocusedOnCurrElem->readInnerXml() . PHP_EOL
			. "Local Name " . $readerFocusedOnCurrElem->localName;

		parent::__construct(
			$message
		);
	}

}