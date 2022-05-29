<?php declare(strict_types=1);

namespace GAR\Database\DBAdapter;

/**
 * Simple PDOTemplate container
 * 
 * @phpstan-import-type DatabaseContract from DBAdapter
 */
class PDOTemplate implements QueryTemplate
{
  /**
   * @var \PDOStatement $template - prepared stage object
   */
  protected readonly \PDOStatement $template;

  /**
   * @param \PDOStatement $template - prepared statement
   */
  function __construct(\PDOStatement $template)
  {
    $this->template = $template;
  }

  /**
   * Execute template and return result
   * 
   * @param  array<DatabaseContract> $values - values to execute
   * @return array<string, mixed>
   */
  function exec(array $values): array|bool
  {
    $this->template->execute($values);
    return $this->template->fetchAll(DBAdapter::PDO_F_ALL);
  }

  /**
   * Ignored in this implementation
   * 
   * @return self
   */
  function save(): self
  {
    return $this;
  }

}