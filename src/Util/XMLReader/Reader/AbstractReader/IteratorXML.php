<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Reader\AbstractReader;

use XMLReader;

/**
 * TRAIT ITERATOR XML
 *
 * IMPLEMENTS METHODS IN ABSTRACTXMLREADER
 */
trait IteratorXML
{
	/**
	 * 	ITERATORS METHODS
	 */

  /**
   *  findes concrete node element with concrete
   *  attributes or return outer xml string
   *
   * @return array - mapped attribute of node
   */
	public function current(): array
	{
		$data = [];

		if (!is_null($this->attrs)) {
			foreach ($this->attrs as $name) {
				$data[strtolower($name)] = $this->reader->getAttribute($name);
			}
		} else {
			$data[] = $this->reader->readOuterXml();
		}

		return $data;
	}

  /**
   * Return curr filename
   *
   * @return string
   */
	public function key(): string
	{
		return $this->fileName;
	}

  /**
   * Searching next node with xml element or make reader null
   *
   * @return void
   */
	public function next(): void
	{
		while($this->reader?->read()) {

			if ($this->reader->nodeType == XMLReader::ELEMENT)
			{
				if (is_null($this->elems)) {
					return;
				} else if (in_array($this->reader->localName, $this->elems)) {
					return;
				}
			}
		}
		$this->reader = null;
	}

  /**
   *  Init iteration
   *
   * @return void
   */
	public function rewind(): void
	{
		$ret = $this->openXML($this->pathToXml);
    if (!is_bool($ret)) {
      $this->reader = $ret;
    } else {
      $this->reader = null;
    }
		$this->next();
	}

  /**
   *  Check if reader is null
   *
   * @return bool - flag for end-iteration
   */
	public function valid(): bool
	{
		return !is_null($this->reader);
	}

}