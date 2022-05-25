<?php declare(strict_types=1);

namespace GAR\Database\DBAdapter;

class PDOTemplate implements QueryTemplate
{
  protected ?\PDOStatement $template = null;

  function __construct(\PDOStatement $template)
  {
    $this->template = $template;
  }

  function exec(array $values): array|bool
  {
    $this->template->execute($values);
    return $this->template->fetchAll(PDOObject::F_ALL);
  }

  function save(): self
  {
    return $this;
  }

}