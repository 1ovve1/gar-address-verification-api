<?php declare(strict_types=1);

namespace GAR\Database\DBAdapter;

use RuntimeException;

abstract class LazyInsert
{
  /**
   * @var string - name of table
   */
  private readonly string $tableName;
  /**
   * @var array - template fields
   */
  private readonly array $fields;
  /**
   * @var int - stages count
   */
  private readonly int $stagesCount;
  /**
   * @var int - current stages in template
   */
  private int $currStage = 0;
  /**
   * @var array - buffer of stage values
   */
  private array $stageBuffer = [];

  /**
   * @param string $tableName
   * @param array $fields
   * @param int $stagesCount
   */
  public function __construct(string $tableName, array $fields, int $stagesCount)
  {
    $this->tableName = $tableName;
    $this->fields = $fields;
    $this->stagesCount = $stagesCount;
  }


  /**
   * Create exception if input is incorrect
   * @param string $tableName - name of table
   * @param array $fields - fields to create
   * @param int $stagesCount - stage count
   * @return void
   */
  public static function isValid(string $tableName, array $fields, int $stagesCount) : void
  {
    if ($stagesCount < 1) {
      throw new RuntimeException(
        'PDOTemplate error: stages buffer needs to be more than 0'
      );
    } else if (empty($fields)) {
      throw new RuntimeException(
        'PDOTemplate error: stages buffer needs to be more than 0'
      );
    } else if (empty($tableName)) {
      throw new RuntimeException(
        'PDOTemplate error: stages buffer needs to be more than 0'
      );
    }
  }

  /**
   * @return string
   */
  public function getTableName(): string
  {
    return $this->tableName;
  }

  /**
   * @return int
   */
  public function getStagesCount(): int
  {
    return $this->stagesCount;
  }

  /**
   * @return array
   */
  public function getFields(): array
  {
    return $this->fields;
  }

  /**
   * @return int
   */
  public function getCurrStage(): int
  {
    return $this->currStage;
  }

  /**
   * @param int|null $value
   */
  public function incCurrStage(?int $value = 1): void
  {
    if (is_null($value)) {
      $this->currStage = 0;
    } else {
      $this->currStage = $this->currStage + $value;
    }
  }

  /**
   * @return array
   */
  public function getStageBuffer(): array
  {
    return $this->stageBuffer;
  }

  /**
   * @param array|null $stageBuffer
   */
  public function setStageBuffer(?array $stageBuffer): void
  {
    if (is_null($stageBuffer)) {
      $this->stageBuffer = [];
    } else {
      $this->stageBuffer = array_merge(
        $this->stageBuffer, array_values($stageBuffer)
      );
    }
  }
}