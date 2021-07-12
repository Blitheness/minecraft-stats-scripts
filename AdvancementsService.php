<?php
use GuzzleHttp\Client;
use pcrov\JsonReader\JsonReader;

class AdvancementsService {
    private ?string $_directory;
    private string $_world;
    private Client $_client;

    public function __construct(string $world, ?string $directory = null) {
        $this->_world = $world;
        $this->_directory = $directory;
        //logger()->info('>>' . getenv('API_BASE'));
        //$this->_client = new Client(['base_url' => getenv('API_BASE')]);
    }

    public function ComputeAdvancements() {
        $path = [
            $this->_directory ?? __DIR__,
            $this->_world,
            'advancements',
            '*.json'
        ];
        $pattern = implode(DIRECTORY_SEPARATOR, $path);
        $advancementFileNames = glob($pattern, GLOB_NOSORT);
        
        $client = new Client();

        foreach($advancementFileNames as $path) {
            $parts = explode(DIRECTORY_SEPARATOR, $path);
            $fileName = $parts[count($parts)-1];
            $uuid = str_replace('.json','',$fileName);

            $advancements = [];

            $reader = new JsonReader();
            $reader->open($path);

            $reader->read();
            $reader->read();
            while(strpos($reader->name(), 'minecraft:') !== false) {
                if (strpos($reader->name(), 'minecraft:recipes') === false) {
                    $advancementName = str_replace('minecraft:', '', $reader->name());
                    $advancementComplete = $reader->value()['done'] ?? false;
                    $advancements[$advancementName] = $advancementComplete;

                    // TODO determine date/time of advancement by max date in criteria
                }
                $reader->next();
            }

            logger()->debug('Advancements found', ['player' => $uuid, 'advancements' => $advancements]);
        
            // try {
            //     $res = $this->_client->request('POST', getenv('API_BASE'), []);
            // }
            // catch(\Exception $e) {
            //     logger()->error('HTTP request failed', ['message' => $e->getMessage(), 'stack_trace' => $e->getTraceAsString()]);
            // }
        
            // logger()->info('API call made to update player statistics', ['player' => $uuid]);
        }
    }
}