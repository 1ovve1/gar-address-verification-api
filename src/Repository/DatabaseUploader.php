<?php

declare(strict_types=1);

namespace GAR\Repository;

use GAR\Entity\EntityFactory;
use GAR\Util\XMLReader\FilesFactory;

class DatabaseUploader
{
    /**
     * @var array<string, \GAR\Database\Table\SQL\QueryModel> - mapped array with tables
     */
    private array $tables;

    /**
     * @param EntityFactory $tables - table factory
     */
    public function __construct(EntityFactory $tables)
    {
        $this->tables = [
            'level_obj' => $tables::getObjectLevels(),
            'housetype' => $tables::getHousetype(),
            'addhousetype' => $tables::getAddhousetype(),
            'addr_obj' => $tables::getAddressObjectTable(),
            'houses' => $tables::getHousesTable(),
            'mun_hierarchy' => $tables::getMunTable(),
            'addr_obj_params' => $tables::getAddressObjectParamsTable(),
        ];
    }

    /**
     * Upload databse using GAR files using $readerFactory
     *
     * @param  FilesFactory $readerFactory - reader factory
     * @return void
     */
    public function upload(FilesFactory $readerFactory): void
    {
        $readerGroup = [
            'level_obj' => $readerFactory::execObjectLevels(),
            'housetype' => $readerFactory::execHousetype(),
            'addhousetype' => $readerFactory::execAddhousetype(),
            'addr_obj' => $readerFactory::execAddrObj(),
            'houses' => $readerFactory::execHouses(),
            'mun_hierarchy' => $readerFactory::execMunHierachi(),
            'addr_obj_params' => $readerFactory::execAddressObjParams(),
        ];

        foreach ($readerGroup as $name => $reader) {
            $reader->exec($this->tables[$name]);
        }
    }
}
