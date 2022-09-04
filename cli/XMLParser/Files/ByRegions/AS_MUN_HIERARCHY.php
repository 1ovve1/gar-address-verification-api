<?php declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRegions;

use DB\Models\MunHierarchy;
use CLI\XMLParser\Files\XMLFile;
use DB\ORM\QueryBuilder\QueryBuilder;

class AS_MUN_HIERARCHY extends XMLFile
{
	/**
	 * {@inheritDoc}
	 */
	public static function getTable(): MunHierarchy
	{
		return new MunHierarchy();
	}

	/**
	 * @inheritDoc
	 */
	public static function callbackOperationWithTable(QueryBuilder $table): void
	{
		$table->saveForceInsert();
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
	 */
    public function execDoWork(array &$values, QueryBuilder &$table): void
    {
        $region = $this->getIntRegion();

        if (isset($values['PARENTOBJID']) && $table->getIdAddrObj($region, $values['PARENTOBJID'])->isNotEmptY()) {
            if ($table->getIdHouses($region, $values['OBJECTID'])->isNotEmpty()) {
		        $table->forceInsert([
			        $values['PARENTOBJID'],
			        null,
			        $values['OBJECTID'],
			        $region,
		        ]);
	        } elseif ($table->getIdAddrObj($region, $values['OBJECTID'])->isNotEmpty()) {
                $table->forceInsert([
                    $values['PARENTOBJID'],
                    $values['OBJECTID'],
                    null,
	                $region,
                ]);
            }
        }
    }


}
