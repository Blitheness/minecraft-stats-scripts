<?php
require implode(DIRECTORY_SEPARATOR, [APP_ROOT, 'vendor', 'autoload.php']);
require implode(DIRECTORY_SEPARATOR, [APP_ROOT, 'repositories', 'AdvancementsRepository.php']);
require implode(DIRECTORY_SEPARATOR, [APP_ROOT, 'repositories', 'StatisticsRepository.php']);
require implode(DIRECTORY_SEPARATOR, [APP_ROOT, 'services', 'PlayerDataService.php']);

use Monolog\Handler\RotatingFileHandler;
use Symfony\Component\Dotenv\Dotenv;
use Monolog\Logger;

// Timings start
$rustart = getrusage();

// Read in environment variables
$dotenvPath = implode(DIRECTORY_SEPARATOR, ['.env']);
try
{
    (new Dotenv())->load($dotenvPath);
}
catch (Exception $e)
{
    fwrite(STDERR, 'Error parsing dotenv file: ' . $e->getMessage());
    throw $e;
}

// Set up logger
$logLevel = getenv('LOG_LEVEL');
if ($logLevel === false)
{
    $logLevel = $_ENV['LOG_LEVEL'];
}

$logfilePath = implode(DIRECTORY_SEPARATOR, ['logs', 'stats.log']);
try
{
    $log = new Logger('log');
    $log->pushHandler(new RotatingFileHandler($logfilePath, 3, constant("Monolog\Logger::{$logLevel}"), false, 0644, false));
}
catch (Exception $e)
{
    fwrite(STDERR, 'Error setting up logger: ' . $e->getMessage());
}

// Get world name and working directory from command-line arguments
$options = getopt('w:d', ['world-name:', 'working-directory']);
$world = $options['w'] ?? $options['world-name'] ?? null;
$directory = $options['d'] ?? $options['working-directory'] ?? null;
if($world === null) {
    $msg = "You must specify a world name using -w or --world=";
    logger()->error($msg);
    throw new InvalidArgumentException($msg);
}
putenv('WORLD_NAME='.$world);
$_ENV['WORLD_NAME'] = $world;
putenv('WORKING_DIRECTORY='.$directory);
$_ENV['WORKING_DIRECTORY'] = $directory;

// Functions to expose utilities
function logger(): Logger
{
    global $log;
    return $log;
}

// Execution time helper
function rutime($ru, $rus, $index) {
    return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
     -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
}
