<?php declare(strict_types=1);

namespace GAR\Database\DBAdapter;

use PDOStatement;


class PDOLazyInsertTemplate extends LazyInsert implements QueryTemplate
{
  /**
   * @var PDOObject - database
   */
  private readonly DBAdapter $db;
  /**
   * @var QueryTemplate|null - prepared statement for PDO exec
   */
  private ?QueryTemplate $state = null;
  /**
   * @var string - template code
   */
  private string $template;

  /**
   * @param DBAdapter $db - database
   * @param string $tableName - name of table
   * @param array $fields - fields to insert
   * @param int $stagesCount - stage count
   */
  public function __construct(DBAdapter $db,
                              string $tableName,
                              array $fields,
                              int $stagesCount = 1)
  {
    $this->db = $db;
    $this->isValid($tableName, $fields, $stagesCount);

    parent::__construct($tableName, $fields, $stagesCount);
  }

  /**
   * Prepare statement to execute
   * @param int $stageCount - count of stages vars
   * @return void - string PDO template
   */
  public function genTemplate(int $stageCount) : void
  {
    $this->template = sprintf(
      'INSERT INTO %s (%s) VALUES %s',
      $this->getTableName(),
      implode(', ', $this->getFields()),
      $this->genVars($stageCount),
    );

    $this->setState($this->template);
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
   * @param array $values -
   * @return PDOLazyInsertTemplate
   */
  function exec(array $values) : self
  {
    if (count($this->getFields()) === count($values)) {
      $this->setStageBuffer($values);
      $this->incCurrStage();

      if ($this->getCurrStage() === $this->getStagesCount()) {
        $this->save();
      }
    }
    return $this;
  }

  /**
   * Save changes in database and reset stage buffer
   * @return PDOLazyInsertTemplate - save changes (require for lazy insert)
   */
  function save(): PDOLazyInsertTemplate
  {
    static $customTemplateUse = false;
    if (!empty($this->getStageBuffer())) {
      if ($this->getCurrStage() < $this->getStagesCount()) {

        $this->genTemplate($this->getCurrStage());
        $this->setState($this->getTemplate());
        $customTemplateUse = true;

      } else if (is_null($this->getState()) || $customTemplateUse) {

        $this->genTemplate($this->getStagesCount());
        $this->setState($this->getTemplate());
        $customTemplateUse = false;

      }

      $this->getState()->exec($this->getStageBuffer());

      $this->setStageBuffer(null);
      $this->incCurrStage(null);
    }
    return $this;
  }


  /**
   * @return Querytemplate|null
   */
  public function getState(): ?QueryTemplate
  {
    return $this->state;
  }

  /**
   * @param string $template
   * @return void
   */
  private function setState(string $template): void
  {
    $this->state = $this->db->prepare($template)->getTemplate();
  }

  /**
   * @return string
   */
  public function getTemplate(): string
  {
    return $this->template;
  }

}