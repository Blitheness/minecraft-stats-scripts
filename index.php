<?php
define('APP_ROOT', __DIR__);
require implode(DIRECTORY_SEPARATOR, [APP_ROOT, 'app', 'bootstrap.php']);

$world = env('WORLD_NAME');
$directory = env('WORKING_DIRECTORY', null);
logger()->info('Loading data...', ['world' => $world, 'working_directory' => $directory]);

$dataService = new PlayerDataService($world, $directory);
$dataService->processPlayerData();

// Determine execution time
$ru = getrusage();
echo "This process used " . rutime($ru, $rustart, "utime") . " ms for its computations" . PHP_EOL;
echo "It spent " . rutime($ru, $rustart, "stime") . " ms in system calls" . PHP_EOL;
