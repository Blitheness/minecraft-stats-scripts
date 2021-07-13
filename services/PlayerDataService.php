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
                'User-Agent' => 'minecraft-stats-scripts/0.1.0',
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ],
            RequestOptions::SYNCHRONOUS => false,
        ]);
    }

    public function processPlayerData()
    {
        $promises = [];

        // Advancements
        $advancementPaths = $this->_advancementsRepo->getAdvancementFilePaths();
        foreach($advancementPaths as $path) {
            $uuid = $this->getUuidFromPath($path);
            $advancements = $this->_advancementsRepo->getAdvancementsForPlayer($uuid);
            $promises[] = $this->_httpClient->requestAsync(
                'POST', 
                env('ADVANCEMENTS_ENDPOINT'), 
                [RequestOptions::JSON => $advancements]
            );
        }

        // Statistics
        $statisticsPaths = $this->_statisticsRepo->getStatisticsFilePaths();
        foreach($statisticsPaths as $path) {
            $uuid = $this->getUuidFromPath($path);
            $statistics = $this->_statisticsRepo->getStatisticsForPlayer($uuid);
            $promises[] = $this->_httpClient->requestAsync(
                'POST', 
                env('STATISTICS_ENDPOINT'), 
                [RequestOptions::JSON => $statistics]
            );
        }

        // Wait for API calls to complete
        try
        {
            $promiseIterator = new EachPromise($promises, [
                'concurrency' => 5,
                'rejected' => function(RequestException $e) {
                    logger()->error('HTTP Request Failed', [
                        'method' => $e->getRequest()->getMethod(),
                        'target' => $e->getRequest()->getRequestTarget(),
                        'status_code' => $e->getResponse()->getStatusCode(), 
                        'error_message' => $e->getMessage()
                    ]);
                }
            ]);
            $promiseIterator->promise()->wait();
        }
        catch (Exception $e)
        {
            logger()->error('HTTP Client Error', [$e->getMessage()]);
        }
    }

    private function getUuidFromPath(string $path): string
    {
        $parts = explode(DIRECTORY_SEPARATOR, $path);
        $fileName = $parts[count($parts)-1];
        return str_replace('.json', '', $fileName);
    }
}