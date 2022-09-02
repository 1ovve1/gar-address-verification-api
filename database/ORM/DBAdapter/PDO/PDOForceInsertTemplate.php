<?php declare(strict_types=1);

namespace DB\ORM\DBAdapter\PDO;


use DB\Exceptions\BadQueryResultException;
use DB\Exceptions\IncorrectBufferInputException;
use DB\Exceptions\QueryTemplateNotFoundException;
use DB\ORM\DBAdapter\{
	DBAdapter,
	InsertBuffer,
	QueryResult,
	QueryTemplate
};

/**
 * Lazy Insert SQL object Using PDO
 *
 * That is the simple decorator that implements QueryTemplate and contains
 * simple QueryTemplate state inside. Then you call exec method of this class
 * this PDOForceInsertTemplate object fill values into self stageBuffer.
 * If you call exec method and stageBuffer is full off, object automatically call save() method,
 * creating template and execute it, also calling reset() method to clear buffers.
 * You can also call save() method when you need it, but notice that
 * class creating new template any time then you call save() with stageBuffer
 * that have random count of values
 *
 * @phpstan-import-type DatabaseContract from DBAdapter
 */
class PDOForceInsertTemplate extends InsertBuffer implements QueryTemplate
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
     * @param String[] $fields - fields of prepared table
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
	    return sprintf(
	        'INSERT INTO %s (%s) VALUES %s',
	        $this->getTableName(),
	        implode(', ', $this->getTableFields()),
	        $this->genVarsFromCurrentGroupNumber(),
	    );
    }


	/**
	 * {@inheritDoc}
	 */
    public function exec(array $values = []): QueryResult
    {
		try{
			$this->setBuffer($values);
		} catch (IncorrectBufferInputException $incorrectBufferInputException) {
			throw new BadQueryResultException("unused", $incorrectBufferInputException);
		}

        if ($this->isBufferFull()) {
            $queryResult = $this->save();
        }
        return $queryResult ?? new PDOQueryResult(null);
    }

	/**
	 * {@inheritDoc}
	 */
    public function save(): QueryResult
    {
        if ($this->isBufferNotEmpty()) {
			try {
				$tryGetState = $this->getState();
			} catch (QueryTemplateNotFoundException) {
				$tryGetState = $this->createNewStateWithCurrentGroupNumber();
			}

	        $queryResult = match($this->isBufferFull()) {
				true => $tryGetState->exec($this->getBuffer()),
		        false => $tryGetState->exec($this->getBufferSlice())
	        };

            $this->resetBufferCursor();
        }

        return $queryResult ?? new PDOQueryResult(null);
    }


	/**
	 * Return state using cursor value
	 * @return QueryTemplate|null
	 * @throws QueryTemplateNotFoundException
	 */
    public function getState(): QueryTemplate|null
    {
        $currentGroupNumber = $this->getCurrentNumberOfGroups();
        if (!array_key_exists($currentGroupNumber, $this->states)) {
          throw new QueryTemplateNotFoundException();
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
      $newTemplate = $this->db->prepare($newTemplate);
      $this->states[$this->getCurrentNumberOfGroups()] = $newTemplate;

      return $newTemplate;
    }
}
