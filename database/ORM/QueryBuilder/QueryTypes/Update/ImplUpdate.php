<?php declare(strict_types=1);

namespace DB\ORM\QueryBuilder\QueryTypes\Update;


class ImplUpdate extends UpdateQuery
{
	function __construct(string $field,
	                     float|int|bool|string|null $value,
	                     string $tableName)
	{
		parent::__construct(
			$this::createQueryBox(
				clearArgs: [$tableName, $field],
				dryArgs: [$value],
			)
		);
	}
}