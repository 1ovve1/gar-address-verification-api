<?php

declare(strict_types=1);

namespace CLI\XMLReader\Reader;

defined('DEFAULT_ZIP_PATH') ?:
    define('DEFAULT_ZIP_PATH', __DIR__ . '/../../../resources/archive/' . $_SERVER['GAR_ZIP_NAME']);
defined('CACHE_PATH') ?:
    define('CACHE_PATH', __DIR__ . '/../../../cache');

class ZipExtract
{
    private readonly \ZipArchive $zipArchive;
    private readonly string $pathToZip;
    private readonly string $cachePath;

    public const CACHE_PATH = CACHE_PATH;
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
     * @throws \RuntimeException - zip not found
     */
    private function checkZipFileExists(string $pathToZip): string
    {
        if (empty($pathToZip) || !file_exists($pathToZip)) {
            if (file_exists($_SERVER['GAR_ZIP_NAME'])) {
                $pathToZip = $_SERVER['GAR_ZIP_NAME'];
            } elseif (file_exists(DEFAULT_ZIP_PATH)) {
                $pathToZip = DEFAULT_ZIP_PATH;
            } else {
                throw new \RuntimeException("Zip file not found {$pathToZip}");
            }
        }

        return $pathToZip;
    }

    /**
     * @param string $cachePath
     * @return string
     * @throws \RuntimeException
     */
    private function checkCachePathExists(string $cachePath): string
    {
        if (empty($pathToZip) || !file_exists($cachePath)) {
            if (file_exists(CACHE_PATH)) {
                $cachePath = CACHE_PATH;
            } else {
                throw new \RuntimeException("Cache path not found {$cachePath}");
            }
        }
        return $cachePath;
    }

    /**
     * @param string $fileName
     * @return bool|string
     */
    public function extractFile(string $fileName): bool|string
    {
        if ($this->zipArchive->open($this->pathToZip)) {
            $extractedPath = $this->tryFindFileInZipArchiveAndExtract($fileName);
            $this->zipArchive->close();
        } else {
            throw new \RuntimeException("Cannot open zip file {$this->pathToZip} to {$this->cachePath}");
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
            $this->tryExtract($realPath);
            return $this->cachePath . "/" . $realPath;
        }
    }

    /**
     * @param string $fileName
     * @return void
     * @throws \RuntimeException
     */
    private function tryExtract(string $fileName): void
    {
        $tryExtract = $this->zipArchive->extractTo($this->cachePath, $fileName);

        if (!$tryExtract) {
            throw new \RuntimeException("Error then try extract file " .
        "'{$fileName}' from zip '{$this->pathToZip}' to '{$this->cachePath}'");
        }
    }
}
