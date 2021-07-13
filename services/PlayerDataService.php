<?php
use GuzzleHttp\Client;

class PlayerDataService
{
    private AdvancementsRepository $_advancementsRepo;
    private StatisticsRepository $_statisticsRepo;
    private Client $_httpClient;

    public function __construct(string $world, ?string $directory = null)
    {
        $basePath = [
            $directory ?? APP_ROOT,
            $world,
        ];
        $this->_advancementsRepo = new AdvancementsRepository($basePath);
        $this->_statisticsRepo = new StatisticsRepository($basePath);

        $apiBase = env('API_BASE');
        $apiKey = env('API_KEY');

        $this->_httpClient = new Client([
            'base_url' => $apiBase,
            'defaults' => [
                'auth' => [
                    null,
                    $apiKey
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'minecraft-stats-scripts/0.1.0',
                ],
            ],
        ]);
    }

    public function processPlayerData()
    {
        // Advancements
        $advancementPaths = $this->_advancementsRepo->getAdvancementFilePaths();
        foreach($advancementPaths as $path) {
            $uuid = $this->getUuidFromPath($path);
            $advancements = $this->_advancementsRepo->getAdvancementsForPlayer($uuid);
            $this->_httpClient->post(env('ADVANCEMENTS_ENDPOINT'), ['body' => $advancements]);
        }
 
        // Statistics
        $statisticsPaths = $this->_statisticsRepo->getStatisticsFilePaths();
        foreach($statisticsPaths as $path) {
            $uuid = $this->getUuidFromPath($path);
            $statistics = $this->_statisticsRepo->getStatisticsForPlayer($uuid);
            $this->_httpClient->post(env('STATISTICS_ENDPOINT'), ['body' => $statistics]);
        }
    }

    private function getUuidFromPath(string $path): string
    {
        $parts = explode(DIRECTORY_SEPARATOR, $path);
        $fileName = $parts[count($parts)-1];
        return str_replace('.json', '', $fileName);
    }
}