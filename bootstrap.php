<?php
require __DIR__ . '/vendor/autoload.php';

use Monolog\Handler\RotatingFileHandler;
use Symfony\Component\Dotenv\Dotenv;
use Monolog\Logger;

// Read in environment variables
(new Dotenv())->load(__DIR__.'/.env');

// Set up logger
$logLevel = getenv('STATS_LOG_LEVEL');
if($logLevel === false) {
    $logLevel = $_ENV['STATS_LOG_LEVEL'];
}
try {
    $log = new Logger('log');
    $log->pushHandler(new RotatingFileHandler(__DIR__.'/logs/stats.log', 3, constant("Monolog\Logger::{$logLevel}"), false, 0644, false));
}
catch(Exception $e) {
    fwrite(STDERR, 'Error setting up logger: ' . $e->getMessage());
}

// Get world from command-line
$options = getopt('w:', ['world:']);
$world = $options['w'] ?? $options['world'] ?? null;
if($world === null) {
    $msg = "You must specify a world name using -w or --world=";
    logger()->error($msg);
    throw new InvalidArgumentException($msg);
}
putenv('WORLD_NAME='.$world);
$_ENV['WORLD_NAME'] = $world;

// Functions to expose utilities
function logger(): Logger
{
    global $log;
    return $log;
}
