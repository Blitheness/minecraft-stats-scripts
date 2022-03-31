<?php
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\EachPromise;

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

        if (! is_dir(implode(DIRECTORY_SEPARATOR, $basePath))) {
            logger()->error('World not found', $basePath);
            exit;
        }

        $this->_advancementsRepo = new AdvancementsRepository($basePath);
        $this->_statisticsRepo = new StatisticsRepository($basePath);

        $apiBase = env('API_BASE');
        $apiKey = env('API_KEY');

        $this->_httpClient = new Client([
            'base_uri' => $apiBase,
            RequestOptions::AUTH => [
                null,
                $apiKey,
            ],
            RequestOptions::HEADERS => [
                'User-Agent' => 'minecraft-stats-scripts/0.1.1',
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ],
            RequestOptions::SYNCHRONOUS => false,
        ]);
    }

    public function processPlayerData()
    {
        // Advancements
        $advancementPaths = $this->_advancementsRepo->getAdvancementFilePaths();
        foreach($advancementPaths as $path) {
            $uuid = $this->getUuidFromPath($path);
            $advancements = $this->_advancementsRepo->getAdvancementsForPlayer($uuid);
            $this->makeJsonPutRequest(
                env('ADVANCEMENTS_ENDPOINT'), 
                [
                    'player_uuid' => $uuid,
                    'data' => $advancements,
                ]
            );
        }

        // Statistics
        $statisticsPaths = $this->_statisticsRepo->getStatisticsFilePaths();
        foreach($statisticsPaths as $path) {
            $uuid = $this->getUuidFromPath($path);
            $statistics = $this->_statisticsRepo->getStatisticsForPlayer($uuid);
            $this->makeJsonPutRequest(
                env('STATISTICS_ENDPOINT'), 
                [
                    'player_uuid' => $uuid,
                    'data' => $statistics,
                ]
            );
        }
    }

    private function makeJsonPutRequest(string $endpoint, array $payload): void
    {
        $this->_httpClient
            ->putAsync(
                $endpoint, 
                [RequestOptions::JSON => $payload]
            )
            ->then(
                null, 
                function(RequestException $e) { $this->logFailedRequest($e); }
            )
            ->wait();
    }

    private function logFailedRequest(RequestException $exception): void
    {
        logger()->error('HTTP Request Failed', [
            'method' => $exception->getRequest()->getMethod(),
            'target' => $exception->getRequest()->getRequestTarget(),
            'status_code' => $exception->getResponse()->getStatusCode(), 
            'error_message' => $exception->getMessage()
        ]);
    }

    private function getUuidFromPath(string $path): string
    {
        $parts = explode(DIRECTORY_SEPARATOR, $path);
        $fileName = $parts[count($parts)-1];
        return str_replace('.json', '', $fileName);
    }
}
