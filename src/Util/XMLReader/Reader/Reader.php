<?php declare(strict_types=1);

namespace GAR\Util\XMLReader\Reader;


use Exception;
use GAR\Database\Table\SQL\QueryModel;
use GAR\Logger\Log;
use GAR\Logger\Msg;
use GAR\Util\XMLReader\Reader\AbstractReader\{AbstractXMLReader,
  IteratorXML,
  OpenXMLFromZip,
  SchedulerObject};
use http\Exception\RuntimeException;

define('DEFAULT_ZIP_PATH', __DIR__ . '/../../../../resources/archive/' . $_SERVER['GAR_ZIP_NAME']);
define('CACHE_PATH', __DIR__ . '/../../../../cache');

class Reader
  extends
    AbstractXMLReader
  implements
    SchedulerObject
{
  use IteratorXML, OpenXMLFromZip;

	/**
	 * simplify construct from abstract reader using Env.php
	 * @param string $fileName name of concrete xml file
	 */
	function __construct($pathToZip = '')
	{
    if (empty($pathToZip)) {
      if (file_exists($_SERVER['GAR_ZIP_NAME'])) {
        $pathToZip = $_SERVER['GAR_ZIP_NAME'];
      } else if (file_exists(DEFAULT_ZIP_PATH)) {
        $pathToZip = DEFAULT_ZIP_PATH;
      } else {
        throw new \RuntimeException("Zip file not found " . $pathToZip);
      }
    }
		parent::__construct($pathToZip,CACHE_PATH);

    // task reporting
		Log::addTask(1);
	}

  private function find

	function __destruct()
	{
		parent::__destruct();

		// task reporting
		Log::removeTask(1);
	}

	/**
	 * method that execute main object function
	 * @param  QueryModel $model model of concrete table
	 * @return void
	 */
	public function exec(QueryModel $model) : void
	{
		foreach ($this as $value) {
			$this->execDoWork($model, $value);
		}

		$model->save();

		$this->__destruct();

		if (!is_null($this->linkToAnother)) {
			$this->linkToAnother->exec($model);
		}
	}

	/**
	 *  method from SchedulerObject
	 *  creating long to the children object by linkToAnother
	 * @param  string $fileName name of concrete file
	 * @return void
	 */
	public function linked(string $fileName) : void
	{
		if (!is_null($this->linkToAnother)) {
			$this->linkToAnother->linked($fileName);
		} else {
      if (empty($this->fileName)) {
        $this->fileName = $fileName;
      } else {
        $this->linkToAnother = new $this($fileName);
      }
		}
	}

	/**
	 *  override method rewind from trait IteratorXML
	 * @return void
	 */
	public function rewind() : void 
	{
		// empty initialization
		if (empty($this->fileName)) {
			return;
		}

    // extract if its none
		if (is_null($this->pathToXml) || !file_exists($this->pathToXml)) {
			try{
				Log::write(Msg::LOG_XML_EXTRACT->value, $this->fileName);
				$this->init();
			} catch (Exception $exception) {
				Log::error($exception, ['fileName' => $this->fileName]);
			}
		}

		// open xml file
    Log::write(Msg::LOG_XML_READ->value, $this->fileName);

    if (!is_null($this->pathToXml)) {
      $ret = $this->openXML($this->pathToXml);
      if (!is_bool($ret)) {
        $this->reader = $ret;
      } else {
        $this->reader = null;
        Log::write('unknown path: ', $this->fileName);
      }
      $this->next();
    }
  }
}