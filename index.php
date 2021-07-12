<?php
require __DIR__ . '/bootstrap.php';

$world = getenv('WORLD_NAME');
logger()->info('Loading data for world...', ['world' => $world]);

// Statistics
$statisticsService = new StatisticsService($world);
$statisticsService->ComputeStatistics();