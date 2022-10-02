<?php declare(strict_types=1);

use DB\ORM\Resolver\AST;

$default_driver = require 'default_driver.php';

// change LIKE to ILIKE for mysql compatibility
$default_driver[AST::COND][AST::COND_LIKE] = 'ILIKE';

return $default_driver;