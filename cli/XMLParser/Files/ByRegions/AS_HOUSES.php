<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRegions;

use DB\Models\Houses;
use CLI\XMLParser\Files\XMLFile;

class AS_HOUSES extends XMLFile
{
	/**
	 * {@inheritDoc}
	 * @return Houses
	 */
	public static function getTable(): Houses
	{
		return new Houses();
	}

	/**
	 * @inheritDoc
	 * @param Houses $table
	 */
	public static function callbackOperationWithTable(mixed $table): void
	{
		$table->saveForceInsert();
	}

	/**
	 * {@inheritDoc}
	 */
	public static function getElement(): string
    {
        return 'HOUSE';
    }

	/**
	 * {@inheritDoc}
	 */
    public static function getAttributes(): array
    {
        return [
            'ISACTUAL' => 'bool',
            'ISACTIVE' => 'bool',
//            'ID' => 'int',
            'OBJECTID' => 'int',
//            'OBJECTGUID' => 'string',
            'HOUSENUM' => 'string',
            'ADDNUM1' => 'string',
            'ADDNUM2' => 'string',
            'HOUSETYPE' => 'int',
            'ADDTYPE1' => 'int',
            'ADDTYPE2' => 'int',
        ];
    }

	/**
	 * {@inheritDoc}
	 * @param array{
	 *     ISACTUAL: bool, ISACTIVE: bool,
	 *     OBJECTID: int, HOUSENUM?: string,
	 *     ADDNUM1?: string, ADDNUM2?: string,
	 *     HOUSETYPE?: int, ADDTYPE1?: int,
	 *     ADDTYPE2?: int} $values
	 * @param Houses $table
	 */
    public function execDoWork(array $values, mixed $table): void
    {
        $region = $this->getIntRegion();

        if ($table->checkIfHousesObjNotExists($region, $values['OBJECTID'])) {

            $table->forceInsert([
				$values['OBJECTID'], $values['HOUSENUM'] ?? null,
	            $values['ADDNUM1'] ?? null, $values['ADDNUM2'] ?? null,
	            $values['HOUSETYPE'] ?? null, $values['ADDTYPE1'] ?? null,
	            $values['ADDTYPE2'] ?? null, $region
            ]);
        }
    }
}
