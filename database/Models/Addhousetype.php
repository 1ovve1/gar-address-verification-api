<?php

declare(strict_types=1);

namespace DB\Models;

use DB\ORM\QueryBuilder\QueryBuilder;


class Addhousetype extends QueryBuilder 
{
	/**
	 * {@inheritDoc}
	 */
	protected static function getFields(): ?array
	{
		return ['id', 'short', 'disc'];
	}

    /**
     * Return fields that need to create in model
     *
     * @return array<string, string>|null
     */
    public function fieldsToCreate(): ?array
    {
        return [
            'id' =>
              'TINYINT UNSIGNED NOT NULL PRIMARY KEY',

            'short' =>
              'CHAR(15)',
        
            'disc' =>
              'CHAR(50)',
        ];
    }
}
