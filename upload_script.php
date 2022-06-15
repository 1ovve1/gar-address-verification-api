<?php declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use GAR\Logger\Log;
use GAR\Repository\DatabaseUploader;

$dotenv = Dotenv::createImmutable(__DIR__ . '/');

if (file_exists(__DIR__ . '/.env')) {
  $dotenv->load();
}

$uplodaer = new DatabaseUploader(new \GAR\Entity\EntityFactory());

$uplodaer->upload(new \GAR\Util\XMLReader\XMLReaderFactory());

