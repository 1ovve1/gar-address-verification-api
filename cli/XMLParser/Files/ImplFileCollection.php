<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files;

use CLI\Processes\ProcessManager;
use CLI\XMLParser\Reader\ReaderVisitor;

class ImplFileCollection implements FileCollection
{
    /** @var array<int, XMLFile> */
    private array $singleFiles = [];
    /** @var array<int, XMLFile> */
    private array $everyRegionFiles = [];
    /** @var String[] */
    private array $listOfRegions;

    /**
     * @param String[] $listOfRegions
     */
    public function __construct(array $listOfRegions)
    {
        $this->listOfRegions = $listOfRegions;

        $this->initFiles();
    }

	private function initFiles(): void
    {
		$handlerConfigParser = new HandlersConfigParser();

		$this->everyRegionFiles = $handlerConfigParser->getHandlersClassesByRegions();
	    $this->singleFiles = $handlerConfigParser->getHandlersClassesByRoot();
    }

    public function exec(ReaderVisitor $reader, array $options = []): void
    {
        foreach ($options as $flag) {
            $isComplete = match ($flag) {
                '--onlyRegions' => $this->readEveryRegions($reader),
                '--onlySingle' => $this->readSingleRegions($reader),
                '--all' => $this->readAll($reader),
                default => false
            };

            if ($isComplete) {
                break;
            }
        }
    }

    private function readSingleRegions(ReaderVisitor &$reader): bool
    {
        foreach ($this->singleFiles as $singleFile) {
            $reader->read($singleFile);
        }

        return true;
    }

    private function readEveryRegions(ReaderVisitor &$reader): bool
    {

        foreach ($this->listOfRegions as $region) {
            foreach ($this->everyRegionFiles as $everyRegionFile) {
                $everyRegionFile->setRegion($region);

                $reader->read($everyRegionFile);
            }
        }

        return true;
    }

    private function readAll(ReaderVisitor &$reader): bool
    {
        $this->readSingleRegions($reader);
        $this->readEveryRegions($reader);
        return true;
    }
}
