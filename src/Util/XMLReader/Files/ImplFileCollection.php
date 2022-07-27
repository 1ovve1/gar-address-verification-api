<?php

declare(strict_types=1);

namespace GAR\Util\XMLReader\Files;

use GAR\Util\XMLReader\Reader\ReaderVisitor;
use JetBrains\PhpStorm\ArrayShape;

class ImplFileCollection implements FileCollection
{
    /** @var XMLFile[] */
    private array $singleFiles = [];
    /** @var XMLFile[] */
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
		$files = $_SERVER['CONFIG']('xml_files_config');

        foreach ($files as $file) {
	        $tryFile = self::getNamespaceFromEnum($file->name);
	        $realName = self::getRealFileNameFromEnum($file->name);


	        try {
                if (key_exists(self::EVERY_REGION_KEY, $tryFile)) {
                    $classFile = $tryFile[self::EVERY_REGION_KEY];
                    $this->everyRegionFiles[] = (new $classFile($realName, ''))->bindType(self::EVERY_REGION_KEY);
                } elseif (key_exists(self::SINGE_KEY, $tryFile)) {
                    $classFile = $tryFile[self::SINGE_KEY];
                    $this->singleFiles[] = (new $classFile($realName))->bindType(self::SINGE_KEY);
                }
            } catch (\Throwable $e) {
                var_dump($tryFile);
                throw new \RuntimeException("Invalid name of class file");
            }
        }
    }

	public const EVERY_REGION_NAMESPACE = "\\EveryRegion\\";
	public const SINGLE_NAMESPACE = "\\Single\\";
	public const EVERY_REGION_KEY = 'every_region';
	public const SINGE_KEY = 'single';

	public static function getRealFileNameFromEnum(string $factName): string
	{
		$realName = '';

		foreach (str_split($factName) as $pos => $char) {
			if (ctype_upper($char) && $pos !== 0) {
				$realName .= '_';
			}
			$realName .= strtoupper($char);
		}

		return $realName;
	}

	#[ArrayShape([
		self::EVERY_REGION_KEY => "string",
		self::SINGE_KEY => "string"
	])]
	public static function getNamespaceFromEnum(string $fileName): array
	{
		$defaultNamespace = "\\" . __NAMESPACE__;
		$tryEveryRegionFlooder = $defaultNamespace . self::EVERY_REGION_NAMESPACE . $fileName;
		$trySingleFlooder = $defaultNamespace . self::SINGLE_NAMESPACE . $fileName;

		if (class_exists($tryEveryRegionFlooder)) {
			$namespace = [
				self::EVERY_REGION_KEY => $tryEveryRegionFlooder,
			];
		} elseif (class_exists($trySingleFlooder)) {
			$namespace = [
				self::SINGE_KEY => $trySingleFlooder,
			];
		} else {
			throw new \RuntimeException("Class {$fileName} not found");
		}

		return $namespace;
	}

    public function exec(ReaderVisitor $reader, array $options = []): void
    {
        foreach ($options as $flag) {
            $isComplete = match ($flag) {
                '--onlyRegions' => $this->readEveryRegions($reader),
                '--onlySingle' => $this->readSingleRegions($reader),
                'all' => $this->readAll($reader),
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
            $singleFile->saveChangesInQueryModel();
        }

        return true;
    }

    private function readEveryRegions(ReaderVisitor &$reader): bool
    {
        $manager = new \CLI\ProcessManager(2);

        foreach ($this->listOfRegions as $region) {
            foreach ($this->everyRegionFiles as $everyRegionFile) {
                $manager->newTask(function() use ($region, $reader, $everyRegionFile) {
                    $reader->read($everyRegionFile->setRegion($region));

                    $everyRegionFile->saveChangesInQueryModel();    
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
