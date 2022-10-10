<?php declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRegions;

use CLI\Models\AddrObjByAddrObjHierarchy;
use CLI\Models\HousesByAddrObjHierarchy;

class AS_ADM_HIERARCHY extends AS_MUN_HIERARCHY
{
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
				$addrObjMap->checkIfMapNotExist($region, $values['PARENTOBJID'], $values['OBJECTID']) &&
				$addrObjMap->checkIfChiledNotExist($region, $values['OBJECTID'])) {
				$addrObjMap->forceInsert([
					$values['PARENTOBJID'],
					$values['OBJECTID'],
					$region,
				]);
			} elseif ($housesMap->checkIfHousesObjExists($region, (int)$values['OBJECTID']) &&
				$housesMap->checkIfMapNotExist($region, $values['PARENTOBJID'], $values['OBJECTID']) &&
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
