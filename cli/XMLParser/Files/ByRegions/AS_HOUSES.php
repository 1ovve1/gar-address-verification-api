<?php

declare(strict_types=1);

namespace CLI\XMLParser\Files\ByRegions;

use DB\Models\Houses;
use CLI\XMLParser\Files\XMLFile;

class AS_HOUSES extends XMLFile
{
	/**
	 * {@inheritDoc}
	 */
	public static function getTable(): Houses
	{
		return new Houses();
	}

	/**
	 * @inheritDoc
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
	 */
    public function execDoWork(array &$values, mixed &$table): void
    {
        $region = $this->getIntRegion();

        if (empty($table->getFirstObjectId($values['OBJECTID'], $region))) {

            foreach ($this::getAttributes() as $attr => $ignore) {
                $values[$attr] ?? $values[$attr] = null;
            }
            unset($values['ISACTUAL']); unset($values['ISACTIVE']);

            $values['REGION'] = $region;

            $table->forceInsert($values);
        }
    }
}
