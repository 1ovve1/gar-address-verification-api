<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Reader\AbstractReader;

use GAR\Database\Table\SQL\QueryModel;

interface SchedulerObject
{
	public function linked(string $fileName) : void;
	public function exec(QueryModel $model) : void;
}
