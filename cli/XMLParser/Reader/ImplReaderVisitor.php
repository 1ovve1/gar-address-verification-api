<?php

declare(strict_types=1);

namespace CLI\XMLParser\Reader;

use CLI\Exceptions\Checked\EmptyXMLElementException;
use CLI\Exceptions\Unchecked\CastException;
use CLI\Exceptions\Unchecked\EmptyDataException;
use CLI\XMLParser\Files\XMLFile;
use RuntimeException;
use XMLReader;

class ImplReaderVisitor implements
    ReaderVisitor
{
    private ZipExtract $zipExtract;
    private XMLReader $xmlReader;

    /**
     * simplify construct from abstract reader using Env.php
     * @param string $pathToZip - path to the zip archive
     * @throws RuntimeException - if zip file not found
     */
    public function __construct(string $pathToZip = '')
    {
	    $this->zipExtract = new ZipExtract($pathToZip);
    }

    /**
     * method that execute main object function
     * @param XMLFile $file
     * @return void
     */
    public function read(XMLFile $file): void
    {
        $extractedPath = $this->extractFileFromZip($file->getPathToFile());
        //todo: rewrite with try catch
        if (is_bool($extractedPath)) {
            return;
        }

        $this->openXmlFile($extractedPath);

		$table = $file::getTable();
        $this->parseXml($file, $table);
		$file::callbackOperationWithTable($table);

        $this->closeReadSessionAndDeleteCache($extractedPath);
    }

    /**
     * @param string $fileName
     * @return string|bool
     */
    protected function extractFileFromZip(string $fileName): string|bool
    {
        return $this->zipExtract->extractFile($fileName);
    }

    /**
     * @param string $pathToExtractedFile
     * @return void
     * @throws RuntimeException
     */
    protected function openXmlFile(string $pathToExtractedFile): void
    {
        $tryOpenXml = XMLReader::open($pathToExtractedFile);
        if (is_bool($tryOpenXml)) {
            throw new RuntimeException("Cannot open XML file into {$pathToExtractedFile}");
        }
        $this->xmlReader = $tryOpenXml;
    }

	/**
	 * @param XMLFile $file
	 * @param mixed $table
	 * @return void
	 */
    private function parseXml(XMLFile &$file, mixed &$table): void
    {
        $elem = $file::getElement();
        $reader = $this->xmlReader;

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === $elem) {
                self::handleElement($file, $reader, $table);
                break;
            }
        }
    }

	/**
	 * @param XMLFile $file
	 * @param XMLReader $readerFocusedOnCurrElem
	 * @param mixed $table
	 * @return void
	 */
    private static function handleElement(XMLFile &$file,
                                          XMLReader &$readerFocusedOnCurrElem,
                                          mixed &$table): void
    {
        $attributesOfElement = $file::getAttributes();
        $elemName = $file::getElement();

        do {

	        try {
				/** @var array<string, DatabaseContract> $data */
		        $data = self::parseElement($attributesOfElement, $readerFocusedOnCurrElem);
		        self::tryUploadDataInTable($file, $table, $data);
	        } catch (EmptyXMLElementException) {
				continue;
	        }

        } while ($readerFocusedOnCurrElem->next($elemName));
    }

	/**
	 * @param String[] $attributesList
	 * @param XMLReader $readerFocusedOnCurrElem
	 * @return Mixed[]
	 * @throws EmptyXMLElementException
	 */
    private static function parseElement(array &$attributesList, XMLReader &$readerFocusedOnCurrElem): array|null
    {
        $data = [];
        if ($readerFocusedOnCurrElem->hasAttributes) {
            foreach ($attributesList as $index => $cast) {
                $value = $readerFocusedOnCurrElem->getAttribute($index);

                if (null !== $value) {
                    self::tryCast($value, $cast);

                    if (is_bool($value) && $value === false) {
                        return null;
                    }
                    $data[$index] = $value;
                }
            }
        } else {
            throw new EmptyXMLElementException();
        }

        if (empty($data)) {
            throw new EmptyDataException($readerFocusedOnCurrElem, $attributesList);
        }
        return $data;
    }

    /**
     * @param mixed $value
     * @param string $cast
     * @return void
     */
    private static function tryCast(mixed &$value, string $cast): void {
        if(false === settype($value, $cast)) {
	        throw new CastException($cast, $value);
        }
    }

	/**
	 * @param XMLFile $file
	 * @param mixed $table
	 * @param array<string, DatabaseContract> &$data
	 * @return void
	 */
    private static function tryUploadDataInTable(XMLFile &$file, mixed &$table, array &$data): void
    {
        try {
            $file->execDoWork($data, $table);
        } catch (\Throwable $e) {
            echo 'values that was tried to upload: ' . PHP_EOL;
            var_dump($data);
            throw new RuntimeException(
                message: 'Error while uploading data in table ' . $file->getFileName(),
                previous: $e
            );
        }
    }

    /**
     * @param string $pathToXmlInCache
     * @return void
     */
    private function closeReadSessionAndDeleteCache(string $pathToXmlInCache): void
    {
        $this->xmlReader->close();

        if (file_exists($pathToXmlInCache)) {
            $deleteTargets = self::getTargetToDelete($pathToXmlInCache);
			self::tryDeleteTargets($deleteTargets);
        }
    }

	/**
	 * @param string $fullXmlPath
	 * @return String[]|null
	 */
    private static function getTargetToDelete(string &$fullXmlPath): null|array
    {
        $splitOfCachePath = str_split(ZipExtract::CACHE_PATH);
        $splitOfXmlPathInCache = str_split($fullXmlPath);

        //validate
        foreach ($splitOfCachePath as $posToCache => $charToCache) {
            if ($charToCache !== $splitOfXmlPathInCache[$posToCache]) {
                return null;
            }
        }

        // find target
        $targets = [];
        $targetPath = ZipExtract::CACHE_PATH . DIRECTORY_SEPARATOR;
        $lenOfXmlPath = strlen($fullXmlPath);
        for ($posToXml = strlen(CACHE_PATH) + 1; $posToXml < $lenOfXmlPath; ++$posToXml) {
            $charInXmlPath = $splitOfXmlPathInCache[$posToXml];
            if ($charInXmlPath === DIRECTORY_SEPARATOR) {
                $targets[] = $targetPath;
            }
            $targetPath .= $charInXmlPath;
        }

        $targets[] = $targetPath;

        return array_reverse($targets);
    }

	/**
	 * @param String[]|null $deleteTargets
	 * @return void
	 */
	private static function tryDeleteTargets(?array $deleteTargets): void
	{
		if (null !== $deleteTargets) {
			foreach ($deleteTargets as $target) {
				self::handleTarget($target);
			}
		}
	}

	private static function handleTarget(string $target): void
	{
		if (is_dir($target)) {
			if (self::isDirEmpty($target)) {
				rmdir($target);
			}
		} else {
			unlink($target);
		}
	}

	private static function isDirEmpty(string $dir): bool
	{
		$handle = opendir($dir);
		if (false === $handle) {
			throw new RuntimeException("Cannot open dir by name {$dir}");
		}
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != "..") {
				closedir($handle);
				return false;
			}
		}
		closedir($handle);
		return true;
	}
}
