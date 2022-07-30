<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files;

use CLI\XMLParser\Reader\ReaderVisitor;
use CLI\ProcessManager;
use JetBrains\PhpStorm\ArrayShape;

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
        $manager = new ProcessManager(1);

        foreach ($this->singleFiles as $singleFile) {
            $manager->newTask(function () use ($singleFile, $reader) {
                $reader->read($singleFile);
                $singleFile->save();
            });
        }

        $manager->waitAll();

        return true;
    }

    private function readEveryRegions(ReaderVisitor &$reader): bool
    {
        $manager = new ProcessManager(2);

        foreach ($this->listOfRegions as $region) {
            foreach ($this->everyRegionFiles as $everyRegionFile) {
                $everyRegionFile->setRegion($region);

                $manager->newTask(function() use ($reader, $everyRegionFile) {
                    $reader->read($everyRegionFile);
                    $everyRegionFile->save();
                }, true);
            }
            $manager->waitAll();
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
