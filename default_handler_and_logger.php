<?php declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
use Monolog\Logger;

/**
 * @param String[] $firstColumn
 * @param String[] $secondColumn
 * @return string
 */
$tableFormatter = function(array $captions, array $data) {
	// first find max len of column element and total width
	$maxWordLength = 0;

	foreach ($captions as $index => $caption) {
		$singleData = $data[$index];

		$maxWordLengthFromCaptionsEscapeEOL = strlen(array_reduce(
			explode("\n", $caption),
			fn(string $acc, string $elem) => (strlen($acc) > strlen($elem)) ? $acc: $elem,
			initial: ''
		));
		$maxWordLengthFromDataEscapeEOL = strlen(array_reduce(
			explode("\n", $singleData),
			fn(string $acc, string $elem) => (strlen($acc) > strlen($elem)) ? $acc: $elem,
			initial: ''
		));

		if ($maxWordLength < $maxWordLengthFromCaptionsEscapeEOL) {
			$maxWordLength = $maxWordLengthFromCaptionsEscapeEOL;
		}
		if ($maxWordLength < $maxWordLengthFromDataEscapeEOL) {
			$maxWordLength = $maxWordLengthFromDataEscapeEOL;
		}
	}

	$totalTableWidth = $maxWordLength + 3;

	// now complete the table
	$table = PHP_EOL . str_repeat('-', $totalTableWidth) . PHP_EOL;
	/**
	 * @param String[] $info
	 * @param int $maxWordLength
	 * @param int $totalTableWidth
	 * @return string
	 */
	$rowCreator = function (array $info) use ($maxWordLength, $totalTableWidth) {
		$row = '';
		$infoHeight = count($info);

		for ($index = 0; $index < $infoHeight; ++$index) {
			$infoElem = $info[$index] ?? '';
			$offsetInfoElem = $maxWordLength - strlen($infoElem);
			$row .= '| ' . $infoElem . str_repeat(' ', $offsetInfoElem) . '|' . PHP_EOL;
		}

		$row .= str_repeat('-', $totalTableWidth) . PHP_EOL;
		return $row;
	};

	foreach ($captions as $rowIndex => $rowTitle) {
		$rowData = explode("\n", $data[$rowIndex]);
		$rowTitle = array_map(
			fn(string $elem) => str_repeat(' ', intdiv($totalTableWidth - strlen($elem), 2) - 3) . $elem,
			explode("\n", $rowTitle)
		);

		$table .= $rowCreator($rowTitle) . $rowCreator($rowData);
	}

	return $table;
};

/**
 * @return Logger
 */
$monolog = function() {
	// init monolog
	$logger = new Logger('runtime');
	$fileHandler = new RotatingFileHandler($_ENV['LOG_PATH'] . '/dump', 2, Level::Notice);
	$systemHandler = new ErrorLogHandler(expandNewlines: true);
	$formatter = new LineFormatter(null, "Y-m-d H-m-s", true, true);

	$fileHandler->setFormatter($formatter);
	$systemHandler->setFormatter($formatter);

	$logger->pushHandler($fileHandler);
	$logger->pushHandler($systemHandler);

	return $logger;
};

set_error_handler(
	function(int $code, string $message, string $errorFileName, int $errorLine)
	use ($monolog, $tableFormatter) {
		$monolog()->error(
			'ERROR WAS FOUND!' .
			$tableFormatter(
				['<<Code>>', '<<Message>>', '<<Error file>>', '<<Error line>>'],
				[(string) $code, $message, $errorFileName, (string) $errorLine]
			)
		);

		return true;
	});

set_exception_handler(
	function(Throwable $ex)
	use ($monolog, $tableFormatter) {
		$monolog()->emergency(
			'UNCHECKED EXCEPTION!' .
			$tableFormatter(
				['<<Message>>', '<<Code>>', '<<Stacktrace>>', '<<Full>>'],
				[$ex->getMessage(), (string)$ex->getCode(), $ex->getTraceAsString(), (string)$ex]
			)
		);
	}
);
$_SERVER['MONOLOG'] = $monolog;