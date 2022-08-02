<?php

declare(strict_types=1);

namespace DB\ORM\DBAdapter\PDO;


use DB\ORM\DBFacade;
use DB\ORM\DBAdapter\{
	DBAdapter, LazyInsert, QueryTemplate
};

/**
 * Lazy Insert SQL object Using PDO
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
     * @var DBAdapter - curr database connection
     */
    private readonly DBAdapter $db;
    /**
     * @var array<QueryTemplate> $states - prepared insert statements
     */
    private array $states = [];

    /**
     * @param DBAdapter $db - database connection
     * @param string $tableName - name of prepared table
     * @param String[] $fields - fields of preapred table
     * @param int $stagesCount - default stages count
     */
    public function __construct(
        DBAdapter $db,
        string $tableName,
        array $fields,
        int $stagesCount = 1
    ) {
        $this->db = $db;

        parent::__construct($tableName, $fields, $stagesCount);
    }

    /**
     * Generate template using current cursor value
     * and create new statement
     *
     * @return string - template
     */
    public function genNewTemplate(): string
    {
        $template = sprintf(
            'INSERT INTO %s (%s) VALUES %s',
            $this->getTableName(),
            implode(', ', $this->getTableFields()),
            $this->genVarsFromCurrentGroupNumber(),
        );

        return $template;
    }


    /**
     * Update buffer (or execute query if buffer full) by $values
     *
     * @param array<DatabaseContract> $values - values to execute
     * @return array<mixed> - ignore
     */
    public function exec(array $values): array
    {
        $this->setBuffer($values);

        if ($this->isBufferFull()) {
            $this->save();
        }
        return [];
    }

    /**
     * Save changes in database and reset stage buffer
     *
     * @return self - self
     */
    public function save(): self
    {
        if ($this->isBufferNotEmpty()) {
            $tryGetState = $this->getState();

            if (is_bool($tryGetState)) {
                $tryGetState = $this->createNewStateWithCurrentGroupNumber();
            }

            if ($this->isBufferFull()) {
                $tryGetState->exec($this->getBuffer());
            } else {
                $tryGetState->exec($this->getBufferSlice());
            }
      

            $this->resetBufferCursor();
        }
        return $this;
    }


    /**
     * Return state using cursor value
     * @return QueryTemplate|bool
     */
    public function getState(): QueryTemplate|bool
    {
        $currentGroupNumber = $this->getCurrentNumberOfGroups();
        if (!array_key_exists($currentGroupNumber, $this->states)) {
            //todo rewrite this warning using logger facade
//      trigger_error("not found index '{$stageIndex}' in stages: return false", E_USER_WARNING);
            return false;
        }
        return $this->states[$currentGroupNumber];
    }

    /**
     * @return QueryTemplate
     */
    public function createNewStateWithCurrentGroupNumber(): QueryTemplate
    {
        $newStrTemplate = $this->genNewTemplate();
        return $this->setState($newStrTemplate);
    }

    /**
     * @param string $newTemplate
     * @return QueryTemplate - new template that was created
     */
    private function setState(string $newTemplate): QueryTemplate
    {
      $newTemplate = $this->db
        ->prepare($newTemplate)
        ->getTemplate();
      $this->states[$this->getCurrentNumberOfGroups()] = $newTemplate;

      return $newTemplate;
    }
}
