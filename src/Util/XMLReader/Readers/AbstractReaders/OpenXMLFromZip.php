<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Readers\AbstractReaders;


use Exception;
use XMLReader;
use ZipArchive;

trait OpenXMLFromZip
{
  /**
   *  Extracting concrete file from zip archive into temp floder
   *
   * @param string $pathToZip - path to zip archive
   * @param string $fileName - name of file or name like /[A-Za-z_-\/]+[0-9]/
   * @param string $cachePath - path to temp floder
   * @return string             return absolute path to extract file
   * @throws Exception
   */
	public function extractFileFromZip(string $pathToZip,
                                     string $fileName,
                                     string $cachePath) : string
	{
		$zip = new ZipArchive;

		if ($zip->open($pathToZip) === TRUE) {

			for ($iter = 0; $iter < $zip->numFiles; ++$iter)
			{
				$realName = $zip->getNameIndex($iter);

				if ($fileName === $realName) 
				{
					break;
				}

				if (preg_match("/" . implode("\/", explode("/", $fileName)) . "_\d+/", $realName)) {
					$fileName = $realName;
					break;
				}
			}
			$zip->extractTo($cachePath, $fileName);
			$zip->close();
		} else {
			throw new Exception(__DIR__ . PHP_EOL . 'invalid operation: open xml file ' .
							$fileName . ' from ' . $pathToZip);
		}

		return $cachePath . '/' . $fileName;
	}

	/**
	 *  Method for open xml files from the path param
	 * @param  string $pathToXml  path to the concrete xml file
	 * @return XMLReader|null    XMLReader object or false
	 */
	public function openXML(string $pathToXml) : XMLReader|bool
	{
    return XMLReader::open($pathToXml);
	}
}