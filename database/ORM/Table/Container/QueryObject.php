<?php

declare(strict_types=1);

namespace DB\ORM\Table\Container;

/**
 * Query object container, implements Query
 */
class QueryObject implements Query
{
    /**
     * @var QueryTypes $type - type of sql query
     */
    private QueryTypes $type;
    /**
     * @var string $rawSql - raw sql code
     */
    private string $rawSql = '';
    /**
     * @var bool $valid - result of validation
     */
    private bool $valid = false;

    /**
     * @param QueryTypes $type - type of sql
     * @return QueryObject - self
     */
    public function setType(QueryTypes $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return QueryTypes - type of sql
     */
    public function getType(): QueryTypes
    {
        return $this->type;
    }

    /**
     * @param string $rawSql - sql code
     * @return QueryObject - self
     */
    public function setRawSql(string $rawSql): self
    {
        $this->rawSql = $rawSql;
        return $this;
    }

    /**
     * @return string - raw sql
     */
    public function getRawSql(): string
    {
        return $this->rawSql;
    }

    /**
     * @param callable $clb - validate function
     * @return $this - self
     */
    public function validate(callable $clb): self
    {
        $this->valid = (bool)$clb($this->getRawSql());
        return $this;
    }

    /**
     * @return bool - result of validation
     */
    public function isValid(): bool
    {
        return $this->valid;
    }
}
