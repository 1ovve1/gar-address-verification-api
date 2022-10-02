<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files;


abstract class XMLFile
{
    private string $fileName = '';
    private ?string $region = null;
    private ?int $intRegion = null;

    /**
     * @param string $fileName
     * @param string|null $region
     */
    public function __construct(string $fileName = '', ?string $region = null)
    {
		if (empty($fileName)) {
			$classPath = explode('\\', static::class);

			$this->fileName = end($classPath);
		}
        if (null !== $region) {
            $this->region = $region;
            $this->intRegion = (int) $region;
        }
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
	 * Here you declare table model that you want to use in your parser model
	 *
	 * @return mixed - db table accessor
	 */
	abstract public static function getTable(): mixed;

	/**
	 * Contains callback procedure that will be executed when file parse is done (using getTable() as argument)
	 * Default do nothing
	 * @param mixed $table - db table accessor from getTable()
	 * @return void
	 */
	public static function callbackOperationWithTable(mixed $table): void
	{}

	/**
	 * procedure that contains main operations from exec method
	 * @param array<DatabaseContract> $values - current parse element
	 * @param mixed $table - table that you return int getTable() method
	 * @return void
	 */
    abstract public function execDoWork(array $values, mixed $table): void;
}
