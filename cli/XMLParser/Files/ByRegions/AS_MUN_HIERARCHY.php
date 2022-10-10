<?php declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRegions;

use CLI\Models\AddrObjByAddrObjHierarchy;
use CLI\Models\HousesByAddrObjHierarchy;
use CLI\XMLParser\Files\XMLFile;

class AS_MUN_HIERARCHY extends XMLFile
{
	/**
	 * {@inheritDoc}
	 * @return array{
	 *     addrObjMap: AddrObjByAddrObjHierarchy,
	 *     housesMap: HousesByAddrObjHierarchy
	 * }
	 */
	public static function getTable(): array
	{
		return [
			'addrObjMap' => new AddrObjByAddrObjHierarchy(),
			'housesMap' => new HousesByAddrObjHierarchy()
		];
	}

	/**
	 * @inheritDoc
	 * @param array{
	 *     addrObjMap: AddrObjByAddrObjHierarchy,
	 *     housesMap: HousesByAddrObjHierarchy
	 * } $table
	 */
	public static function callbackOperationWithTable(mixed $table): void
	{
		$table['addrObjMap']->saveForceInsert();
		$table['housesMap']->saveForceInsert();
	}

	/**
	 * {@inheritDoc}
	 */
	public static function getElement(): string
    {
        return 'ITEM';
    }

	/**
	 * {@inheritDoc}
	 */
    public static function getAttributes(): array
    {
        return [
            'OBJECTID' => 'int',
            'PARENTOBJID' => 'int',
        ];
    }

	/**
	 * {@inheritDoc}
	 * @param array{
	 *     OBJECTID: int,
	 *     PARENTOBJID?: int
	 * } $values
	 * @param array{
	 *     addrObjMap: AddrObjByAddrObjHierarchy,
	 *     housesMap: HousesByAddrObjHierarchy
	 * } $table
	 */
    public function execDoWork(array $values, mixed $table): void
    {
		['addrObjMap' => $addrObjMap, 'housesMap' => $housesMap] = $table;

        $region = $this->getIntRegion();

        if (isset($values['PARENTOBJID']) && $addrObjMap->checkIfAddrObjExist($region, $values['PARENTOBJID'])) {
			// check if chiled objectid are instance of address obj
	        // else check if chiled objectid are instance of houses and etc
            if ($addrObjMap->checkIfAddrObjExist($region, $values['OBJECTID']) &&
	            $addrObjMap->checkIfChiledNotExist($region, $values['OBJECTID'])) {
		        $addrObjMap->forceInsert([
			        $values['PARENTOBJID'],
			        $values['OBJECTID'],
			        $region,
		        ]);
	        } elseif ($housesMap->checkIfHousesObjExists($region, (int)$values['OBJECTID']) &&
	            $housesMap->checkIfChiledNotExist($region, $values['OBJECTID'])) {
                $housesMap->forceInsert([
                    $values['PARENTOBJID'],
                    $values['OBJECTID'],
	                $region,
                ]);
            }
        }
    }


}
