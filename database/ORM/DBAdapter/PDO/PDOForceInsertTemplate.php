<?php declare(strict_types=1);

namespace DB\ORM\DBAdapter\PDO;


use DB\Exceptions\Checked\NullableQueryResultException;
use DB\Exceptions\Checked\QueryTemplateNotFoundException;
use DB\Exceptions\Unchecked\BadQueryResultException;
use DB\ORM\DBAdapter\{DBAdapter, InsertBuffer, QueryResult, QueryTemplate};

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
 */
class PDOForceInsertTemplate extends InsertBuffer implements QueryTemplate
{
    /** @var DBAdapter - curr database connection */
    private readonly DBAdapter $db;
    /** @var array<QueryTemplate> $states - prepared insert statements */
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
	        $this->genVarsFromCurrentBufferCursor(),
	    );
    }


	/**
	 * {@inheritDoc}
	 */
    public function exec(?array $values = null): QueryResult
    {
		$this->setBuffer($values);

        if ($this->isBufferFull()) {
            $queryResult = $this->makeExec();
        }
        return $queryResult ?? new PDOQueryResult(null);
    }

	/**
	 * {@inheritDoc}
	 */
    public function save(): QueryResult
    {
        if ($this->isBufferNotEmpty()) {
			$queryResult = $this->makeExec();
        }

        return $queryResult ?? new PDOQueryResult(null);
    }

	private function makeExec(): QueryResult
	{
		try {
			$tryGetState = $this->getState();
		} catch (QueryTemplateNotFoundException) {
			$tryGetState = $this->createNewStateWithCurrentGroupNumber();
		}

		$queryResult = $tryGetState->exec();

		$this->resetBufferCursor();

		return $queryResult;
	}

	/**
	 * Return state using cursor value
	 * @return QueryTemplate
	 * @throws QueryTemplateNotFoundException
	 */
    public function getState(): QueryTemplate
    {
        $currentBufferCursor = $this->getCurrentBufferCursor();
        if (!array_key_exists($currentBufferCursor, $this->states)) {
          throw new QueryTemplateNotFoundException();
        }
        return $this->states[$currentBufferCursor];
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
	  // danger things here...
	  if (is_a($newTemplate, PDOTemplate::class)) {

		  $bufferColumnFlip = array_flip($this->getTableFields());
		  $currentCursor = $this->getCurrentBufferCursor();
		  $offset = $this->getTableFieldsCount();
		  foreach ($this->buffer as $columnName => &$columnValue) {
			  $columnIndex = $bufferColumnFlip[$columnName];
			  foreach ($columnValue as $rowIndex => &$rowValue) {
				  if ($rowIndex >= $currentCursor) {
					  break;
				  }

				  $bindRes = $newTemplate->template->bindParam($columnIndex + ($rowIndex * $offset) + 1, $rowValue);
				  if (false === $bindRes) {
					  throw new BadQueryResultException("bad try to allocate param by buffer[{$columnIndex}][{$rowIndex}] reference");
				  }
			  }
		  }

	  } else {
		  throw new BadQueryResultException("incompatible type: PDOForceInsert implements should be used with PDOTemplate implement");
	  }

	  $this->states[$this->getCurrentBufferCursor()] = $newTemplate;

      return $newTemplate;
    }
}
