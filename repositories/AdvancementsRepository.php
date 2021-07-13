<?php
use pcrov\JsonReader\JsonReader;

class AdvancementsRepository
{
    private string $_basePath;

    public function __construct(array $basePathParts)
    {
        $basePathParts[] = 'advancements';
        $this->_basePath = implode(DIRECTORY_SEPARATOR, $basePathParts);
    }

    public function getAdvancementFilePaths(): array
    {
        $pattern = implode(DIRECTORY_SEPARATOR, [$this->_basePath, '*.json']);
        $paths = glob($pattern, GLOB_NOSORT);
        return $paths;
    }

    /**
     * @param string $uuid Player UUID
     */
    public function getAdvancementsForPlayer(string $uuid): array
    {
        $path = implode(DIRECTORY_SEPARATOR, [$this->_basePath, $uuid . '.json']);

        $parts = explode(DIRECTORY_SEPARATOR, $path);
        $fileName = $parts[count($parts)-1];
        $uuid = str_replace('.json','',$fileName);

        $advancements = [];

        $reader = new JsonReader();
        $reader->open($path);

        $reader->read();
        $reader->read();
        while (strpos($reader->name(), 'minecraft:') !== false)
        {
            if ($this->isNotRecipeAdvancement($reader->name()))
            {
                $advancementName = $this->stripAdvancementNamespace($reader->name());
                $advancementData = $reader->value();

                $advancement = [
                    'complete' => $advancementData['done'] ?? false, 
                    'completed_at' => null
                ];
                
                if ($advancement['complete'] && is_array($advancementData['criteria']))
                {
                    $mostRecentDate = null;
                    foreach ($advancementData['criteria'] as $date)
                    {
                        $currentDate = new DateTimeImmutable($date);
                        if ($mostRecentDate === null)
                        {
                            $mostRecentDate = new DateTimeImmutable($date);
                        }
                        else
                        {
                            if($mostRecentDate < $currentDate)
                            {
                                $mostRecentDate = $currentDate;
                            }
                        }
                    }
                    $advancement['completed_at'] = $mostRecentDate;
                }

                $advancements[$advancementName] = $advancement;
            }
            $reader->next();
        }
        logger()->debug('Advancements found', ['player' => $uuid, 'advancements' => $advancements]);

        return $advancements;
    }

    private function isNotRecipeAdvancement(?string $advancementName): bool
    {
        if ($advancementName === null) {
            return false;
        }
        return strpos($advancementName, 'minecraft:recipes') === false;
    }

    private function stripAdvancementNamespace(string $advancementName): string
    {
        return explode(':', $advancementName, 2)[1];
    }
}