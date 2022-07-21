<?php

declare(strict_types=1);

namespace GAR\Database\DBAdapter;

/**
 * Common query tempalte interface for prepared statements
 *
 * @phpstan-import-type DatabaseContract from DBAdapter
 */
interface QueryTemplate
{
    /**
     * Execute statement
     *
     * @param  array<DatabaseContract> $values - values to execute
     * @return array<mixed>
     */
    public function exec(array $values): array;

    /**
     * Accept changes in template (use for lazy insert)
     *
     * @return mixed
     */
    public function save(): mixed;
}
