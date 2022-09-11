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
$tableFormatter = function(array $firstColumn, array $secondColumn) {
	// first find max len of column element and total width
	$maxWordLengthFromFirstColumn = 0; $maxWordLengthFromSecondColumn = 0;
	foreach ($firstColumn as $index => $firstColumnData) {
		$secondColumnData = $secondColumn[$index];
		$maxWordLengthFromFirstColumnEscapeEOL = strlen(array_reduce(
			explode("\n", $firstColumnData),
			fn($acc, $elem) => (strlen($acc ?? '') > strlen($elem ?? '')) ? $acc: $elem
		));
		$maxWordLengthFromSecondColumnsEscapeEOL = strlen(array_reduce(
			explode("\n", $secondColumnData),
			fn($acc, $elem) => (strlen($acc ?? '') > strlen($elem) ?? '') ? $acc: $elem
		));
		if ($maxWordLengthFromFirstColumn < $maxWordLengthFromFirstColumnEscapeEOL) {
			$maxWordLengthFromFirstColumn = $maxWordLengthFromFirstColumnEscapeEOL;
		}
		if ($maxWordLengthFromSecondColumn < $maxWordLengthFromSecondColumnsEscapeEOL) {
			$maxWordLengthFromSecondColumn = $maxWordLengthFromSecondColumnsEscapeEOL;
		}
	}

	$totalTableWidth = $maxWordLengthFromFirstColumn + $maxWordLengthFromSecondColumn + 6;

	// now complete the table
	$table = PHP_EOL . str_repeat('-', $totalTableWidth) . PHP_EOL;
	foreach ($firstColumn as $rowIndex => $rowTitle) {
		$rowTitle = explode("\n", $rowTitle);
		$rowData = explode("\n", $secondColumn[$rowIndex]);

		$biggestRowHeight = (count($rowTitle) > count($rowData)) ? count($rowTitle): count($rowData);

		for ($index = 0; $index < $biggestRowHeight; ++$index) {
			$rowTitleElem = $rowTitle[$index] ?? '';
			$rowDataElem = $rowData[$index] ?? '';
			$offsetTitleElem = $maxWordLengthFromFirstColumn - strlen($rowTitleElem);
			$offsetDataElem = $maxWordLengthFromSecondColumn - strlen($rowDataElem);

			$table .= '| ' . $rowTitleElem . str_repeat(' ', $offsetTitleElem);
			$table .= " | " . $rowDataElem . str_repeat(' ', $offsetDataElem) . ' |' . PHP_EOL;
		}

		$table .= str_repeat('-', $totalTableWidth) . PHP_EOL;
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
				['Field', 'Code', 'Message', 'Error file', 'Error line'],
				['Error info', (string) $code, $message, $errorFileName, (string) $errorLine]
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
				['Field', 'Message', 'Code', 'Stacktrace', 'Full'],
				['Exception info', $ex->getMessage(), (string)$ex->getCode(), $ex->getTraceAsString(), (string)$ex]
			)
		);
	}
);

$_SERVER['MONOLOG'] = $monolog;