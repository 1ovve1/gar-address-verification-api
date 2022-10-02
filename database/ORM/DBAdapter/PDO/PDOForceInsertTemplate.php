<?php declare(strict_types=1);

namespace DB\ORM\DBAdapter\PDO;


use DB\Exceptions\Checked\QueryTemplateNotFoundException;
use DB\Exceptions\Unchecked\IncorrectBufferInputException;
use DB\ORM\DBAdapter\{DBAdapter, InsertBuffer, QueryResult, QueryTemplate, QueryTemplateBindAble};

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
    /** @var array<QueryTemplateBindAble> $states - prepared insert statements */
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
		if (empty($values)) {
			throw new IncorrectBufferInputException($this->getTableFieldsCount(), $values);
		}

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
	 * @return QueryTemplateBindAble
	 * @throws QueryTemplateNotFoundException
	 */
    public function getState(): QueryTemplateBindAble
    {
        $currentBufferCursor = $this->getCurrentBufferCursor();
        if (!array_key_exists($currentBufferCursor, $this->states)) {
          throw new QueryTemplateNotFoundException();
        }
        return $this->states[$currentBufferCursor];
    }

    /**
     * @return QueryTemplateBindAble
     */
    public function createNewStateWithCurrentGroupNumber(): QueryTemplateBindAble
    {
        $newStrTemplate = $this->genNewTemplate();
        return $this->setState($newStrTemplate);
    }

    /**
     * @param string $newTemplate
     * @return QueryTemplateBindAble - new template that was created
     */
    private function setState(string $newTemplate): QueryTemplateBindAble
    {
      $newTemplate = $this->db->prepare($newTemplate);

	  $this->bufferReshape();
	  $newTemplate->bindParams(
		  $this->buffer, true
	  );

	  $this->states[$this->getCurrentBufferCursor()] = $newTemplate;

      return $newTemplate;
    }

}
