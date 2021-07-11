<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap.php';

use pcrov\JsonReader\JsonReader;

$world = getenv('WORLD_NAME');
logger()->info('Loading data for world...', ['world' => $world]);

// Statistics
$path = [
    __DIR__,
    $world,
    'stats',
    '*.json'
];
$pattern = implode(DIRECTORY_SEPARATOR, $path);
$statsFileNames = glob($pattern, GLOB_NOSORT);

foreach($statsFileNames as $path) {
    $parts = explode(DIRECTORY_SEPARATOR, $path);
    $fileName = $parts[count($parts)-1];
    $uuid = str_replace('.json','',$fileName);
    logger()->debug('Processing statistics', ['player' => $uuid, 'world' => $world]);

    $reader = new JsonReader();
    
    $reader->open($path);
    $reader->read('minecraft:mined');
    $miningStats = getMiningStats($reader->value());
    $farmingStats = getFarmingStats($reader->value());

    $reader->open($path);
    $reader->read('minecraft:killed');
    $slayerStats = getSlayerStats($reader->value());

    $reader->open($path);
    $reader->read('minecraft:crafted');
    $cookingStats = getCookingStats($reader->value());

    $reader->open($path);
    $reader->read('minecraft:custom');
    $farmingStats = hydrateBreedingStatistic($farmingStats, $reader->value());

    $reader->open($path);
    $reader->read('minecraft:used');
    $farmingStats = hydrateHoeStatistic($farmingStats, $reader->value());

    $reader->open($path);
    $reader->read('minecraft:picked_up');
    $farmingStats = hydrateSugarCaneStatistic($farmingStats, $reader->value());
    
    $stats = [
        'mining' => $miningStats,
        'farming' => $farmingStats,
        'slayer' => $slayerStats,
        'cooking' => $cookingStats
    ];
    logger()->info('Statistics calculated', ['player' => $uuid, 'stats' => $stats]);

    $client = new GuzzleHttp\Client();
    $res = $client->request('POST', getenv('API_BASE'), $stats);

    logger()->info('API call made to update player statistics', ['player' => $uuid]);
}

function hydrateSugarCaneStatistic(array $stats, ?array $data): array
{
    if($data === null) {
        return $stats;
    }

    $stats['sugar_cane'] += $data['minecraft:sugar_cane'] ?? 0;

    return $stats;
}

function hydrateHoeStatistic(array $stats, ?array $data): array
{
    if($data === null) {
        return $stats;
    }

    $stats['hoe_used'] += $data['minecraft:wooden_hoe'] ?? 0;
    $stats['hoe_used'] += $data['minecraft:stone_hoe'] ?? 0;
    $stats['hoe_used'] += $data['minecraft:iron_hoe'] ?? 0;
    $stats['hoe_used'] += $data['minecraft:gold_hoe'] ?? 0;
    $stats['hoe_used'] += $data['minecraft:diamond_hoe'] ?? 0;
    $stats['hoe_used'] += $data['minecraft:netherite_hoe'] ?? 0;

    return $stats;
}

function hydrateBreedingStatistic(array $stats, ?array $data): array
{
    if($data === null) {
        return $stats;
    }

    $stats['breeding'] += $data['minecraft:animals_bred'] ?? 0;

    return $stats;
}

function getFarmingStats(?array $data): array
{
    $stats = [
        'breeding' => 0,
        'carrots' => 0,
        'hoe_used' => 0,
        'melon' => 0,
        'potatoes' => 0,
        'pumpkin' => 0,
        'sugar_cane' => 0,
        'wheat' => 0,
    ];

    if($data === null) {
        return $stats;
    }

    foreach($data as $item => $amount) {
        switch($item) {
            case 'minecraft:carrots':
                $stats['carrots'] += $amount;
                break;
            case 'minecraft:melon':
                $stats['melon'] += $amount;
                break;
            case 'minecraft:potatoes':
                $stats['potatoes'] += $amount;
                break;
            case 'minecraft:pumpkin':
                $stats['pumpkin'] += $amount;
                break;
            case 'minecraft:wheat':
                $stats['wheat'] += $amount;
                break;
        }
    }

    return $stats;
}

function getCookingStats(?array $data): array
{
    $stats = [
        'beef' => 0,
        'bread' => 0,
        'cake' => 0,
        'chicken' => 0,
        'cookie' => 0,
        'fish' => 0,
        'kelp' => 0,
        'mutton' => 0,
        'porkchop' => 0,
        'potato' => 0,
        'rabbit' => 0,
        'stew' => 0,
    ];

    if($data === null) {
        return $stats;
    }

    foreach($data as $item => $amount) {
        switch($item) {
            case 'minecraft:baked_potato':
                $stats['potato'] += $amount;
                break;
            case 'minecraft:bread':
                $stats['bread'] += $amount;
                break;
            case 'minecraft:cake':
                $stats['cake'] += $amount;
                break;
            case 'minecraft:cookie':
                $stats['cookie'] += $amount;
                break;
            case 'minecraft:cooked_beef':
                $stats['beef'] += $amount;
                break;
            case 'minecraft:cooked_chicken':
                $stats['chicken'] += $amount;
                break;
            case 'minecraft:cooked_cod':
            case 'minecraft:cooked_salmon':
                $stats['fish'] += $amount;
                break;
            case 'minecraft:cooked_mutton':
                $stats['mutton'] += $amount;
                break;
            case 'minecraft:cooked_porkchop':
                $stats['porkchop'] += $amount;
                break;
            case 'minecraft:cooked_rabbit':
                $stats['rabbit'] += $amount;
                break;
            case 'minecraft:dried_kelp':
                $stats['kelp'] += $amount;
                break;
            case 'minecraft:suspicious_stew':
                $stats['stew'] += $amount;
                break;
        }
    }

    return $stats;
}

function getSlayerStats(?array $data): array
{
    $stats = [
        'creeper' => 0,
        'drowned' => 0,
        'enderman' => 0,
        'ender_dragon' => 0,
        'hoglin' => 0,
        'husk' => 0,
        'magma_cube' => 0,
        'phantom' => 0,
        'piglin' => 0,
        'pillager' => 0,
        'silverfish' => 0,
        'skeleton' => 0,
        'slime' => 0,
        'spider' => 0,
        'vindicator' => 0,
        'witch' => 0,
        'wither' => 0,
        'wither_skeleton' => 0,
        'zombie' => 0,
    ];

    if($data === null) {
        return $stats;
    }

    foreach($data as $item => $amount) {
        switch($item) {
            case 'minecraft:creeper':
                $stats['creeper'] += $amount;
                break;
            case 'minecraft:drowned':
                $stats['drowned'] += $amount;
                break;
            case 'minecraft:enderman':
                $stats['enderman'] += $amount;
                break;
            case 'minecraft:ender_dragon':
                $stats['ender_dragon'] += $amount;
                break;
            case 'minecraft:hoglin':
                $stats['hoglin'] += $amount;
                break;
            case 'minecraft:husk':
                $stats['husk'] += $amount;
                break;
            case 'minecraft:magma_cube':
                $stats['magma_cube'] += $amount;
                break;
            case 'minecraft:phantom':
                $stats['phantom'] += $amount;
                break;
            case 'minecraft:piglin':
            case 'minecraft:zombified_piglin':
            case 'minecraft:piglin_brute':
                $stats['piglin'] += $amount;
                break;
            case 'minecraft:pillager':
                $stats['pillager'] += $amount;
                break;
            case 'minecraft:silverfish':
                $stats['silverfish'] += $amount;
                break;
            case 'minecraft:skeleton':
                $stats['skeleton'] += $amount;
                break;
            case 'minecraft:slime':
                $stats['slime'] += $amount;
                break;
            case 'minecraft:spider':
                $stats['spider'] += $amount;
                break;
            case 'minecraft:vindicator':
                $stats['vindicator'] += $amount;
                break;
            case 'minecraft:witch':
                $stats['witch'] += $amount;
                break;
            case 'minecraft:wither':
                $stats['wither'] += $amount;
                break;
            case 'minecraft:wither_skeleton':
                $stats['wither_skeleton'] += $amount;
                break;
            case 'minecraft:zombie':
            case 'minecraft:zombie_villager':
                $stats['zombie'] += $amount;
                break;
        }
    }

    return $stats;
}

function getMiningStats(?array $data): array
{
    $stats = [
        'coal' => 0,
        'copper' => 0,
        'diamonds' => 0,
        'glowstone' => 0,
        'gold' => 0,
        'iron' => 0,
        'lapis' => 0,
        'quartz' => 0,
        'redstone' => 0,
    ];

    if($data === null) {
        return $stats;
    }

    foreach($data as $item => $amount) {
        switch($item) {
            case 'minecraft:diamond_ore':
            case 'minecraft:deepslate_diamond_ore':
                $stats['diamonds'] += $amount;
                break;
            case 'minecraft:glowstone':
                $stats['glowstone'] += $amount;
                break;
            case 'minecraft:iron_ore':
            case 'minecraft:deepslate_iron_ore':
                $stats['iron'] += $amount;
                break;
            case 'minecraft:lapis_ore':
            case 'minecraft:deepslate_lapis_ore':
                $stats['lapis'] += $amount;
                break;
            case 'minecraft:redstone_ore':
            case 'minecraft:deepslate_redstone_ore':
                $stats['redstone'] += $amount;
                break;
            case 'minecraft:nether_quartz_ore':
                $stats['quartz'] += $amount;
                break;
            case 'minecraft:copper_ore':
            case 'minecraft:deepslate_copper_ore':
                $stats['copper'] += $amount;
                break;
            case 'minecraft:nether_gold_ore':
                $stats['gold'] += (int) ceil($amount / 9);
                break;
            case 'minecraft:gold_ore':
            case 'minecraft:deepslate_gold_ore':
                $stats['gold'] += $amount;
                break;
            case 'minecraft:coal_ore':
            case 'minecraft:deepslate_coal_ore':
                $stats['coal'] += $amount;
                break;
        }
    }

    return $stats;
}