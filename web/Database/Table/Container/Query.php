<?php

declare(strict_types=1);

namespace GAR\Database\Table\Container;

/**
 * Common query container interface
 */
interface Query
{
    /**
     * Return type of query
     * @return QueryTypes
     */
    public function getType(): QueryTypes;

    /**
     * Return raw query string
     * @return string
     */
    public function getRawSql(): string;

    /**
     * Check raw query by validation callback
     * @return boolean
     */
    public function isValid(): bool;
}
