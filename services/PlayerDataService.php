<?php
class PlayerDataService
{
    private AdvancementsRepository $_advancementsRepo;
    private StatisticsRepository $_statisticsRepo;

    public function __construct(string $world, ?string $directory = null)
    {
        $dataBasePath = [
            $directory ?? APP_ROOT,
            $world,
        ];
        $this->_advancementsRepo = new AdvancementsRepository($dataBasePath);
        $this->_statisticsRepo = new StatisticsRepository($dataBasePath);
    }

    public function processPlayerData()
    {
        // Advancements
        $advancementPaths = $this->_advancementsRepo->getAdvancementFilePaths();
        foreach($advancementPaths as $path) {
            $uuid = $this->getUuidFromPath($path);
            $advancements = $this->_advancementsRepo->getAdvancementsForPlayer($uuid);
        }
 
        // Statistics
        $statisticsPaths = $this->_statisticsRepo->getStatisticsFilePaths();
        foreach($statisticsPaths as $path) {
            $uuid = $this->getUuidFromPath($path);
            $statistics = $this->_statisticsRepo->getStatisticsForPlayer($uuid);
        }
    }

    private function getUuidFromPath(string $path): string
    {
        $parts = explode(DIRECTORY_SEPARATOR, $path);
        $fileName = $parts[count($parts)-1];
        return str_replace('.json', '', $fileName);
    }
}