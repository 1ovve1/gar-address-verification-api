<?php

declare(strict_types=1);

namespace GAR\Logger;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
use Monolog\Logger;
use PDOException;
use Psr\Log\LoggerInterface;
use Throwable;

define('LOG_PATH', __DIR__ . '/../../logs');
set_exception_handler([Log::class, 'error']);

/**
 * SIMPLE LOGGER CLASS
 */
class Log
{
    public static ?Logger $logger = null;

    /**
     *  getting message to log and additional params (strings)
     * @param string $message message to log
     * @param string|null ...$params other (maybe name of files or other info)
     * @return void
     */
    public static function write(string $message, ?string ...$params): void
    {
        if ($_ENV['SWOOLE_ENABLE'] === 'true' &&
        !(key_exists('argv', $_SERVER) &&
        count($_SERVER['argv']) >= 2 &&
        in_array($_SERVER['argv'][1], ['-l', '--log'], true))) {
            return;
        }
        if (null === self::$logger) {
            self::launch();
        }

        self::put(sprintf(
            "%s %s %s",
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
    public static function warning(string $message): void
    {
    }

    /**
     * error method (making caption if error was throw)
     * @param Throwable $exception
     * @param array $params additional strings
     * @return void
     */
    public static function error(Throwable $exception, array $params = []): void
    {
        if ($_ENV['SWOOLE_ENABLE'] === 'true' &&
        !(key_exists('argv', $_SERVER) &&
        count($_SERVER['argv']) >= 2 &&
        in_array($_SERVER['argv'][1], ['-l', '--log'], true))) {
            return;
        }

        if ($exception instanceof PDOException) {
            $msg = Msg::LOG_DB_BAD->value;
        }

        if (null === self::$logger) {
            self::launch();
        }

        $message = sprintf(
            "%s\n%s\n%s\n",
            Msg::LOG_BAD->value,
            $exception,
            http_build_query($params, '', ', ')
        );

        self::put($message);
        self::$logger->error($message);
    }

    /**
     * create log directory if it does-nt exists
     * @return void
     */
    private static function launch(): void
    {
        self::$logger = new Logger('runtime');
        self::$logger->pushHandler(new RotatingFileHandler(LOG_PATH . '/my', 2, Level::Notice));
        
        self::$logger->notice(PHP_EOL . "url: " . urldecode($_SERVER['REQUEST_URI'] ?? '') . PHP_EOL);

        self::put(Msg::LOG_LAUNCH->value . PHP_EOL);
    }

    /**
     * write message to the log file and echo
     * (if script run with -l or --log flag)
     * @param  string $message message to log
     * @return void
     */
    private static function put(string $message): void
    {
        self::$logger->notice(PHP_EOL . $message);
        if (key_exists('argv', $_SERVER) &&
            count($_SERVER['argv']) >= 2 &&
            in_array($_SERVER['argv'][1], ['-l', '--log'], true)) {
            echo "\r" . $message;

            if (self::addTask() > self::removeTask()) {
                echo sprintf(
                    "Прогресс: %d%% (%d из %d)",
                    self::removeTask() * 100 / (self::addTask()),
                    self::removeTask(),
                    self::addTask()
                );
            }
        }
    }

    /**
     * add task to progress bar
     * @param 	int $add task weight
     * @return 	int  	total tasks count
     */
    public static function addTask(int $add = 0): int
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
    public static function removeTask(int $dec = 0): int
    {
        static $taskRemoved = 0;

        if ($dec < 0) {
            $taskRemoved = 0;
        }

        $taskRemoved += $dec;

        return $taskRemoved;
    }

    public static function getInstance(): LoggerInterface
    {
        if (null === self::$logger) {
            self::launch();
        }
        return self::$logger;
    }
}
