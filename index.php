<?php
$rustart = getrusage();
require __DIR__ . '/bootstrap.php';

$world = getenv('WORLD_NAME');
logger()->info('Loading data for world...', ['world' => $world]);

// Statistics
$statisticsService = new StatisticsService($world);
$statisticsService->ComputeStatistics();

// Advancements
$advancementsService = new AdvancementsService($world);
$advancementsService->ComputeAdvancements();

// Determine execution time
function rutime($ru, $rus, $index) {
    return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
     -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
}
$ru = getrusage();
echo "This process used " . rutime($ru, $rustart, "utime") .
    " ms for its computations" . PHP_EOL;
echo "It spent " . rutime($ru, $rustart, "stime") .
    " ms in system calls" . PHP_EOL;