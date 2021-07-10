<?php
use Symfony\Component\Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Read in environment variables
(new Dotenv())->load(__DIR__.'/.env');

// Get world from command-line
$options = getopt('w:', ['world:']);
$world = $options['w'] ?? $options['world'];
putenv('WORLD_NAME='.$world);
$_ENV['WORLD_NAME'] = $world;

// Set up logger
$log = new Logger('log');
$log->pushHandler(new StreamHandler(__DIR__.'/stats.log', Logger::INFO));

// Functions to expose utilities
function logger(): Logger
{
    global $log;
    return $log;
}
