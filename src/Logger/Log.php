<?php declare(strict_types=1);

namespace GAR\Logger;


use PDOException;
use Throwable;

define('LOG_PATH', __DIR__ . '/../../logs');
set_exception_handler([Log::class, 'error']);

/**
 * SIMPLE LOGGER CLASS
 */
class Log
{

    /**
     *  getting message to log and additional params (strings)
     * @param string $message message to log
     * @param string|null ...$params other (maybe name of files or other info)
     * @return void
     */
	public static function write(string $message, ?string ...$params) : void
	{
//		if (!defined('CURR_LOG_FILE')) {
//			self::launch();
//		}

		self::put(sprintf("[%s]: %s %s %s", 
			self::currTime(),
			$message,
			implode(' ', $params), 
			PHP_EOL
		));
	}

	/**
	 * warning method (making caption if exception was throw)
	 * @param  string $message message to log
	 * @return void
	 */
	public static function warning(string $message) : void
	{

	}

  /**
   * error method (making caption if error was throw)
   * @param Throwable $exception
   * @param array $params additional strings
   * @return void
   */
	public static function error(Throwable $exception, array $params = []) : void
	{

		if ($exception instanceof PDOException) {
			$msg = Msg::LOG_DB_BAD->value;
		}

		self::put(sprintf("[%s]: %s\n%s\n%s\n",
			self::currTime(), 
			Msg::LOG_BAD->value,
			$exception,
			http_build_query($params, '', ', ')
		));
	}

	/**
	 * create log directory if it does-nt exists
	 * @return void
	 */
	private static function launch() : void
	{
		if (!file_exists(LOG_PATH)) {
      mkdir(LOG_PATH);
    }

    define('CURR_LOG_FILE', sprintf("%s/log_%s.txt",
			LOG_PATH, 
			str_replace(' ', '_', self::currTime())
		));

		self::put(Msg::LOG_LAUNCH->value . PHP_EOL);
	} 

	/**
	 * write message to the log file and echo
	 * (if script run with -l or --log flag)
	 * @param  string $message message to log
	 * @return void
	 */
	private static function put(string $message) : void 
	{
//	  echo $message;
//		if (count($_SERVER['argv']) >= 2 &&
//			in_array($_SERVER['argv'][1], ['-l', '--log'])) {
//      file_put_contents(CURR_LOG_FILE, $message, FILE_APPEND);
//			echo "\r" . $message;
//
//			if (self::addTask() > self::removeTask()) {
//				echo sprintf("Прогресс: %d%% (%d из %d)",
//					self::removeTask() * 100 / (self::addTask()),
//					self::removeTask(),
//					self::addTask()
//				);
//			}
//		}
	}

	/**
	 * add task to progress bar
	 * @param 	int $add task weight
	 * @return 	int  	total tasks count
	 */
	public static function addTask(int $add = 0) : int 
	{
		static $tasksCount = 0;

//		if ($add < 0) {
//			$tasksCount = -1;
//		}

		$tasksCount += $add;

		return $tasksCount;
	}

	/**
	 * remove task in a progress bar 
	 * @param  int $dec weight of task
	 * @return int         removed task count
	 */
	public static function removeTask(int $dec = 0) : int
	{
		static $taskRemoved = 0;

		if ($dec < 0) {
			$taskRemoved = 0;
		}

		$taskRemoved += $dec;

		return $taskRemoved;
	}

	/**
	 * return curr message format
	 * @return string
	 */
	private static function currTime() : string
	{
		return date('Y-m-d H:i:s');
	}
}