<?php

declare(strict_types=1);

namespace GAR\Util\XMLReader\Files;

use GAR\Util\XMLReader\Reader\ReaderVisitor;

class ImplFileCollection implements FileCollection
{
    /** @var XMLFile[] */
    private array $singleFiles = [];
    /** @var XMLFile[] */
    private array $everyRegionFiles = [];
    /** @var String[] */
    private array $listOfRegions;

    /**
     * @param array $listOfRegions
     */
    public function __construct(array $listOfRegions)
    {
        $this->listOfRegions = $listOfRegions;

        $this->initFiles();
    }

    private function initFiles(): void
    {
        foreach (ConfigList::cases() as $elem) {
            $tryFile = ConfigList::getNamespaceFromEnum($elem);
            $realName = ConfigList::getRealFileNameFromEnum($elem);

            try {
                if (key_exists(ConfigList::EVERY_REGION_KEY, $tryFile)) {
                    $classFile = $tryFile[ConfigList::EVERY_REGION_KEY];
                    $this->everyRegionFiles[] = (new $classFile($realName, ''))->bindType(ConfigList::EVERY_REGION_KEY);
                } elseif (key_exists(ConfigList::SINGLE_KEY, $tryFile)) {
                    $classFile = $tryFile[ConfigList::SINGLE_KEY];
                    $this->singleFiles[] = (new $classFile($realName))->bindType(ConfigList::SINGLE_KEY);
                }
            } catch (\Throwable $e) {
                var_dump($tryFile);
                throw new \RuntimeException("Invalid name of class file");
            }
        }
    }


    public function exec(ReaderVisitor $reader): void
    {
        foreach ($this->singleFiles as $singleFile) {
            $reader->read($singleFile);
            $singleFile->saveChangesInQueryModel();
        }

        foreach ($this->everyRegionFiles as $everyRegionFile) {
            foreach ($this->listOfRegions as $region) {
                $reader->read($everyRegionFile->setRegion($region));
      
                $everyRegionFile->saveChangesInQueryModel();
            }
        }
    }
}
