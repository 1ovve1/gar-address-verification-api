<?php declare(strict_types=1);

namespace GAR\Database\DBAdapter;

use PDOStatement;

/**
 * Lazy Insert Template object Using PDO
 *
 * That is the simple decarator that implements QueryTemplate and contains
 * simple QueryTemplate state inside. Then you call exec method of this class
 * this PDOLazyInsertTemplate object fill values into self stageBuffer.
 * If you call exec method and stageBuffer is full off, object automaticly call save() method,
 * creating template and execute it, also calling reset() method to clear buffers.
 * You can also call save() method when you need it, but notice that
 * class creating new template any time then you call save() with stageBuffer 
 * that have random count of values
 * 
 * @phpstan-import-type DatabaseContract from DBAdapter
 */
class PDOLazyInsertTemplate extends LazyInsert implements QueryTemplate
{
  /**
   * @var DBAdapter $db - curr database connection
   */
  private readonly DBAdapter $db;
  /**
   * @var QueryTemplate - prepared insert statement
   */
  private QueryTemplate $state;
  /**
   * @var string $template - default string template (by default stages count)
   */
  private string $template;

  /**
   * @param DBAdapter $db - database connection
   * @param string $tableName - name of prepared table
   * @param array<string> $fields - fields of preapred table
   * @param int $stagesCount - default stages count
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
   * Generate template using $stageCount and create new statement
   * 
   * @param int $stageCount - count of stages vars
   * @return void
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
   * Generate vars for prepared statement (in PDO: '?')
   * Return example: (?, ..., ?) ... (?, ... , ?) multiple by $stageCount times
   * 
   * @return string - string of vars
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
   * Update buffer (or execute query if buffer full) by $values 
   * 
   * @param array<DatabaseContract> $values - values to execute
   * @return self - self
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
   * 
   * @return self - self
   */
  function save(): self
  {
    static $customTemplateUse = false;
    if (!empty($this->getStageBuffer())) {
      if ($this->getCurrStage() < $this->getStagesCount()) {

        $this->genTemplate($this->getCurrStage());
        $this->setState($this->getTemplate());
        $customTemplateUse = true;

      } else if ($customTemplateUse) {

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
   * Return curr exempl
   * @return QueryTemplate
   */
  public function getState(): QueryTemplate
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