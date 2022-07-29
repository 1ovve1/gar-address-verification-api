<?php

declare(strict_types=1);

namespace CLI\XMLReader\Files;

use GAR\Database\Table\SQL\QueryModel;

abstract class XMLFile
{
    private string $fileName = '';
    private ?string $region = null;
    private ?int $intRegion = null;

    /**
     * @param string $fileName
     * @param string|null $region
     */
    public function __construct(string $fileName, ?string $region = null)
    {
        $this->fileName = $fileName;
        if (null !== $region) {
            $this->region = $region;
            $this->intRegion = (int) $region;
        }
    }

    public function save()
    {
        static::getQueryModel()->save();
    }


    /**
     * @return string
     */
    public function getRegion(): string
    {
        if (null === $this->region) {
            throw new \RuntimeException("Try get the null region 
      (replace file to EveryRegion flooder if you wanna use regions)");
        }
        return $this->region;
    }

    /**
     * @return int
     */
    public function getIntRegion(): int
    {
        if (null === $this->intRegion) {
            throw new \RuntimeException("Try get the null region 
      (replace file to EveryRegion flooder if you wanna use regions)");
        }
        return $this->intRegion;
    }

    /**
     * @param string $region
     * @return XMLFile
     */
    public function setRegion(string $region): self
    {
        $this->region = $region;
        $this->intRegion = (int)$region;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @return string
     */
    public function getPathToFile(): string
    {
        if (null === $this->region) {
            return $this->fileName;
        } else {
            return $this->region . '/' . $this->fileName;
        }
    }

    /**
     * return concrete table model that support current file
     * @return QueryModel
     */
    abstract public static function getQueryModel(): QueryModel;

    public function saveChangesInQueryModel(): void
    {
        static::getQueryModel()->save();
    }

    /**
     * return elements of xml document
     * @return string elements names
     */
    abstract public static function getElement(): string;

    /**
     * return attributes of elements in xml document
     * @return String[] attributes names
     */
    abstract public static function getAttributes(): array;


    /**
     * procedure that contains main operations from exec method
     * @param array &$values current parse element
     * @return void
     */
    abstract public function execDoWork(array &$values): void;
}
