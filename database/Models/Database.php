<?php

declare(strict_types=1);

namespace DB\Models;

use DB\ORM\ConcreteTable;


class Database extends ConcreteTable
{
	/**
	 * @inheritDoc
	 */
	public static function getInstance(bool $createMetaTable = false): ConcreteTable
	{
		return parent::getInstance($createMetaTable);
	}

}
