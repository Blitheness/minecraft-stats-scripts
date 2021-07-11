<?php

use Monolog\Handler\RotatingFileHandler;
use Symfony\Component\Dotenv\Dotenv;
use Monolog\Logger;

// Read in environment variables
(new Dotenv())->load(__DIR__.'/.env');

// Set up logger
$log = new Logger('log');
$log->pushHandler(new RotatingFileHandler(__DIR__.'/logs/stats.log', 3, Logger::INFO, false, 0644, false));

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
