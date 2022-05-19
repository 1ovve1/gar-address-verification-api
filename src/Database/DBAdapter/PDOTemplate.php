<?php declare(strict_types=1);

namespace GAR\Database\DBAdapter;

use PDOStatement;


class PDOTemplate extends LazyInsert implements InsertTemplate
{
  /**
   * @var PDOStatement|null - prepared statement for PDO exec
   */
  private ?PDOStatement $state = null;
  /**
   * @var string - template code
   */
  private string $template;

  /**
   * @param string $tableName
   * @param array $fields
   * @param int $stagesCount
   */
  public function __construct(string $tableName, array $fields, int $stagesCount = 1)
  {
    $this->isValid($tableName, $fields, $stagesCount);

    parent::__construct($tableName, $fields, $stagesCount);
  }

  /**
   * Prepare statement to execute
   * @param int $stageCount - count of stages vars
   * @return void - string PDO template
   */
  public function genTemplate(DBAdapter $db, int $stageCount) : void
  {
    $this->template = sprintf(
      'INSERT INTO %s (%s) VALUES %s',
      $this->getTableName(),
      implode(', ', $this->getFields()),
      $this->genVars($stageCount),
    );

    $this->setState($db, $this->template);
  }

  /**
   * Return vars in string view (?, ?, ..., ?)
   * @return string - vars in string view
   */
  public function genVars(int $stageCount) : string
  {
    $vars = [];

    for ($stage = $stageCount; $stage > 0; $stage--) {
      $temp = [];
      foreach ($this->getFields() as $ignored) {
        $temp[] = '? ';
      }
      $vars[] = '(' . implode(', ', $temp) . ')';
    }

    return implode(', ', $vars);
  }

  /**
   * Execute template with bind values (use lazy method)
   * @param DBAdapter $db - database connection
   * @param array $values -
   * @return PDOTemplate
   */
  function exec(DBAdapter $db, array $values) : self
  {
    if (count($this->getFields()) === count($values)) {
      $this->setStageBuffer($values);
      $this->incCurrStage();

      if ($this->getCurrStage() === $this->getStagesCount()) {
        $this->save($db);
      }
    }
    return $this;
  }

  /**
   * Save changes in database and reset stage buffer
   * @param DBAdapter $db - curr connection
   * @return PDOTemplate - save changes (require for lazy insert)
   */
  function save(DBAdapter $db): PDOTemplate
  {
    static $customTemplateUse = false;
    if (!empty($this->getStageBuffer())) {
      if ($this->getCurrStage() < $this->getStagesCount()) {
        $this->genTemplate($db, $this->getCurrStage());
        $this->setState($db, $this->getTemplate());
        $customTemplateUse = true;
      } else if (is_null($this->getState()) || $customTemplateUse) {
        $this->genTemplate($db, $this->getStagesCount());
        $this->setState($db, $this->getTemplate());
        $customTemplateUse = false;
      }

      $this->getState()->execute($this->getStageBuffer());

      $this->setStageBuffer(null);
      $this->incCurrStage(null);
    }
    return $this;
  }


  /**
   * @return PDOStatement|null
   */
  public function getState(): ?PDOStatement
  {
    return $this->state;
  }

  /**
   * @param DBAdapter $db
   * @param string $template
   * @return void
   */
  private function setState(DBAdapter $db, string $template): void
  {
    $this->state = $db->prepare($template);
  }

  /**
   * @return string
   */
  public function getTemplate(): string
  {
    return $this->template;
  }

}