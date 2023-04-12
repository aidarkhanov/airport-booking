<?php declare(strict_types=1);

define('DEBUG', filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN));
error_reporting(E_ALL);
ini_set('display_errors', DEBUG ? 'on' : 'off');

define('DB_HOST', getenv('DB_HOST'));
