<?php
define('APP_ROOT', __DIR__);
require implode(DIRECTORY_SEPARATOR, [APP_ROOT, 'app', 'bootstrap.php']);

$world = getenv('WORLD_NAME');
logger()->info('Loading data for world...', ['world' => $world]);

$dataService = new PlayerDataService($world);
$dataService->processPlayerData();

// Determine execution time
$ru = getrusage();
echo "This process used " . rutime($ru, $rustart, "utime") . " ms for its computations" . PHP_EOL;
echo "It spent " . rutime($ru, $rustart, "stime") . " ms in system calls" . PHP_EOL;
