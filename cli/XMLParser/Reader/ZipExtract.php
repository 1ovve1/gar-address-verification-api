<?php

declare(strict_types=1);

namespace CLI\XMLParser\Reader;

use RuntimeException;

defined('CUSTOM_ZIP_PATH') ?:
	define('CUSTOM_ZIP_PATH', $_ENV['GAR_ZIP_NAME']);
defined('DEFAULT_ZIP_PATH') ?:
    define('DEFAULT_ZIP_PATH', $_ENV['ARCHIVE_PATH'] . '/' . $_ENV['GAR_ZIP_NAME']);
defined('CACHE_PATH') ?:
    define('CACHE_PATH', $_ENV['CACHE_PATH']);

class ZipExtract
{
    private readonly \ZipArchive $zipArchive;
    private readonly string $pathToZip;
    private readonly string $cachePath;

	public const CACHE_PATH = CACHE_PATH;
	public const CUSTOM_ZIP_PATH = CUSTOM_ZIP_PATH;
	public const DEFAULT_ZIP_PATH = DEFAULT_ZIP_PATH;

    /**
     * @param string $pathToZip
     * @param string $cachePath
     */
    public function __construct(string $pathToZip, string $cachePath = '')
    {
        $this->pathToZip = $this->checkZipFileExists($pathToZip);
        $this->cachePath = $this->checkCachePathExists($cachePath);
        $this->zipArchive = new \ZipArchive();
    }

    /**
     * @param string $pathToZip - path to concrete zip file
     * @return string - zip path
     * @throws RuntimeException - zip not found
     */
    private function checkZipFileExists(string $pathToZip): string
    {
        if (empty($pathToZip) || !file_exists($pathToZip)) {
            if (file_exists(self::CUSTOM_ZIP_PATH)) {
                $pathToZip = self::CUSTOM_ZIP_PATH;

            } elseif (file_exists(self::DEFAULT_ZIP_PATH)) {
                $pathToZip = self::DEFAULT_ZIP_PATH;

            } else {
                throw new RuntimeException("Zip file not found {$pathToZip}");
            }
        }

        return $pathToZip;
    }

    /**
     * @param string $cachePath
     * @return string
     * @throws RuntimeException - cache path not found
     */
    private function checkCachePathExists(string $cachePath): string
    {
        if (!file_exists($cachePath)) {
            if (file_exists(CACHE_PATH)) {
                $cachePath = self::CACHE_PATH;
            } else {
                throw new RuntimeException("Cache path not found {$cachePath}");
            }
        }
        return $cachePath;
    }

    /**
     * @param string $fileName
     * @return bool|string
     * @throws RuntimeException - cannot open zip file
     */
    public function extractFile(string $fileName): bool|string
    {
        if ($this->zipArchive->open($this->pathToZip)) {
            $extractedPath = $this->tryFindFileInZipArchiveAndExtract($fileName);
            $this->zipArchive->close();
        } else {
            throw new RuntimeException("Cannot open zip file {$this->pathToZip} to {$this->cachePath}");
        }

        return $extractedPath;
    }

    /**
     * @param string $fileName
     * @return bool|string
     */
    private function tryFindFileInZipArchiveAndExtract(string $fileName): bool|string
    {
        $realPath = null;

        for ($iter = 0; $iter < $this->zipArchive->numFiles; ++$iter) {
            $tryPath = $this->zipArchive->getNameIndex($iter);
            if (preg_match("/" . implode("\/", explode("/", $fileName)) . "_\d+/", $tryPath)) {
                $realPath = $tryPath;
                break;
            }
        }

        if (null === $realPath) {
	        return false;
        } else {
			//TODO: add logger here to show whar type of $realPath we extract now
	        $this->tryExtract($realPath);
            return $this->cachePath . "/" . $realPath;
        }
    }

    /**
     * @param string $fileName
     * @return void
     * @throws RuntimeException - error then try extract file from zip
     */
    private function tryExtract(string $fileName): void
    {
        $tryExtract = $this->zipArchive->extractTo($this->cachePath, $fileName);

        if (!$tryExtract) {
            throw new RuntimeException("Error then try extract file " .
        "'{$fileName}' from zip '{$this->pathToZip}' to '{$this->cachePath}'");
        }
    }
}
