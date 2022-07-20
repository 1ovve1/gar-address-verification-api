<?php declare(strict_types=1);

namespace GAR\Database\DBAdapter;

use RuntimeException;

/**
 * Lazy insert abstract class
 *
 * @phpstan-import-type DatabaseContract from DBAdapter
 */
abstract class LazyInsert
{
  /**
   * @var string $tableName - name of table
   */
  private readonly string $tableName;
  /**
   * @var array<mixed> $fields - template fields
   */
  private readonly array $fields;
  /**
   * @var int $stagesCount - stages count
   */
  private readonly int $stagesCount;
  /**
   * @var int $currStage - current stages in template
   */
  private int $currStage = 0;
  /**
   * @var int $stageBufferCursor - cursor of the stage buffer index
   */
  private int $stageBufferCursor = 0;
  /**
   * @var SplFixedArray $stageBuffer - buffer of stage values
   */
  private \SplFixedArray $stageBuffer;

  /**
   * @param string $tableName - name of table
   * @param array<mixed> $fields - fields in table
   * @param int $stagesCount - stage buffer size
   */
  public function __construct(string $tableName, array $fields, int $stagesCount)
  {
    $this->tableName = $tableName;
    $this->fields = $fields;
    $this->stagesCount = $stagesCount;
    $this->stageBuffer = new \SplFixedArray($stagesCount * count($fields));
  }


  /**
   * Create exception if input is incorrect
   * 
   * @param string $tableName - name of table
   * @param array<mixed> $fields - fields to create
   * @param int $stagesCount - stage count
   * @return void
   * @throws RuntimeException
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
   * Return table name
   * @return string
   */
  public function getTableName(): string
  {
    return $this->tableName;
  }

  /**
   * Return max stage buffer count
   * @return int
   */
  public function getStagesCount(): int
  {
    return $this->stagesCount;
  }

  /**
   * Return fields in table template
   * @return array<mixed>
   */
  public function getFields(): array
  {
    return $this->fields;
  }

  /**
   * Return curr stage count
   * @return int
   */
  public function getCurrStage(): int
  {
    return $this->currStage;
  }

  /**
   * Increment curr stage by $value (set by default) or clear if $value = null
   * @param int|null $value - value to increment
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
   * Return curr stage buffer
   * @return array
   */
  public function getStageBuffer(): array
  {
    return $this->stageBuffer->toArray();
  }

  public function getStageBufferLimited(): array 
  {
    $array = [];
    for($i = 0; $i < $this->stageBufferCursor; ++$i) {
      array_push($array, $this->stageBuffer[$i]);
    }

    return $array;
  }

  /**
   * Set stage buffer by $insertValues or reset it by $insertValues = null
   * @param array<DatabaseContract>|null $insertValues - values that need add in $stageBuffer 
   */
  public function setStageBuffer(?array $insertValues): void
  {
    if (is_null($insertValues)) {
      $this->stageBufferCursor = 0;
    } else {
      foreach ($insertValues as $value) {
        $this->stageBuffer[$this->stageBufferCursor] = $value;
        $this->stageBufferCursor++;
      }
    }
  }
}