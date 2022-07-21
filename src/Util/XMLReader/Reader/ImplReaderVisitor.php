<?php

declare(strict_types=1);

namespace GAR\Util\XMLReader\Reader;

use Exception;
use GAR\Logger\Log;
use GAR\Logger\Msg;
use GAR\Util\XMLReader\Files\XMLFile;

class ImplReaderVisitor implements
    ReaderVisitor
{
    private ZipExtract $zipExtract;
    private \XMLReader $xmlReader;

    /**
     * simplify construct from abstract reader using Env.php
     * @param string $pathToZip - path to the zip archive
     * @throws \RuntimeException - if zip file not found
     */
    public function __construct(string $pathToZip = '')
    {
        $this->initZipExtract($pathToZip);
    }

    private function initZipExtract(string $pathToZip): void
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
        if (is_bool($extractedPath)) {
            return;
        }
        $this->openXmlFile($extractedPath);

        $this->parseXml($file);

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
     * @throws \RuntimeException
     */
    protected function openXmlFile(string $pathToExtractedFile): void
    {
        $tryOpenXml = \XMLReader::open($pathToExtractedFile);
        if (is_bool($tryOpenXml)) {
            throw new \RuntimeException("Cannot open XML file into {$pathToExtractedFile}");
        }
        $this->xmlReader = $tryOpenXml;
    }

    private function parseXml(XMLFile $file): void
    {
        $elem = $file::getElement();
        $reader = $this->xmlReader;
    
        while ($reader->read()) {
            if ($reader->nodeType == \XMLReader::ELEMENT && $reader->localName == $elem) {
                $this->handleElement($file);
                break;
            }
        }
    }

    private function handleElement(XMLFile $file): void
    {
        $elem = $file::getElement();
        $reader = $this->xmlReader;
    
        do {
            $breakFlag = false;
            $data = [];

            foreach ($file::getAttributes() as $index => $cast) {
                if ($value = $reader->getAttribute($index)) {
                    try {
                        settype($value, $cast);
                    } catch (\ValueError $error) {
                        throw new \RuntimeException(
                            "Incorrect type of the attribute in " . $file::class . PHP_EOL .
              "Given type '{$cast}' for cast attribute '{$index}'" . PHP_EOL
                        );
                    }

                    $data[$index] = $value;
                } elseif ($cast === 'bool') {
                    $breakFlag = true;
                    break;
                }
            }
            if (!$breakFlag) {
                $file->execDoWork($data);
            }
        } while ($reader->next($elem));
    }

    private function closeReadSessionAndDeleteCache(string $pathToXmlInCache): void
    {
        $this->xmlReader->close();
        unlink($pathToXmlInCache);
    }
}
