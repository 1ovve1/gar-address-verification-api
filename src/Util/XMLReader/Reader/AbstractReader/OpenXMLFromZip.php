<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Reader\AbstractReader;


use Exception;
use XMLReader;
use ZipArchive;

trait OpenXMLFromZip
{
  /**
   *  Extracting concrete file from zip archive into temp floder
   *
   * @param string $pathToZip - path to zip archive
   * @param string $fileName 	-	name of file or name like /[A-Za-z_-\/]+[0-9]/
   * @param string $cachePath - path to temp floder
   * @return string|null       return absolute path to extract file
   */
	public function extractFileFromZip(string $pathToZip,
                                     string $fileName,
                                     string $cachePath) : null|string
	{
		$zip = new ZipArchive;

		if ($zip->open($pathToZip)) {

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
			$tryExtract = $zip->extractTo($cachePath, $fileName);
			$zip->close();

			if (!$tryExtract) {
				trigger_error("File {$fileName} not found");
				return null;
			}
		}

		return $cachePath . '/' . $fileName;
	}

	/**
	 *  Method for open xml files from the path param
	 * @param  string|null 			$pathToXml  path to the concrete xml file
	 * @return XMLReader|bool   XMLReader object or false
	 */
	public function openXML(string $pathToXml) : XMLReader|bool
	{
		if (is_null($pathToXml)) {
			return false;
		} else {
    	return XMLReader::open($pathToXml);
		}
	}
}