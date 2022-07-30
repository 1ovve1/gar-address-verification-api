<?php declare(strict_types=1);

const SERVER_START = true;

require __DIR__ . '/../web/App/App.php';

if (isset($app)) {
  $app->run();
}