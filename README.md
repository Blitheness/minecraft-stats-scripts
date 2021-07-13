# Minecraft Stats Scripts
## Overview
Scripts to parse Minecraft statistics and advancement JSON files, and send relevant information to an API endpoint. The API receiving the data can then decide how to further process it, according to the needs of the application.

## Requirements
- PHP 8.0
- PHP Ctype and intl extensions
- [Composer](https://getcomposer.org "Dependency Manager for PHP")

## Usage
1. Clone this repository
2. Enter the directory containing `composer.lock`
3. Run `composer install`
4. Set up a cron job to run this program on a schedule

Run on a schedule (cron job), at an interval of your choice. I suggest every 30 minutes.
```
php index.php --working-directory="/home/minecraft" --world-name="world"
```
or (short-hand arguments):
```
php index.php -d "/home/minecraft" -w "world"
```

The working directory argument is optional. It will default to the directory where the script is located.

## Example API Calls
### Advancements
```
POST /api/advancements HTTP/1.1
User-Agent: minecraft-stats-scripts/0.1.0
Accept: application/json
Content-Type: application/json

{
  "player_uuid": "00000000-0000-0000-0000-000000000001",
  "data": {
    "story/obtain_armor": {
      "complete": true,
      "completed_at": {
        "date": "2021-07-03 17:32:38.000000",
        "timezone_type": 1,
        "timezone": "+00:00"
      }
    },
    "husbandry/balanced_diet": {
      "complete": false,
      "completed_at": null
    },
    "husbandry/bred_all_animals": {
      "complete": false,
      "completed_at": null
    },
    "story/root": {
      "complete": true,
      "completed_at": {
        "date": "2021-07-03 16:12:43.000000",
        "timezone_type": 1,
        "timezone": "+00:00"
      }
    }
  }
}
```

### Statistics (Skills)
```
POST /api/statistics HTTP/1.1
User-Agent: minecraft-stats-scripts/0.1.0
Accept: application/json
Content-Type: application/json

{
  "player_uuid": "00000000-0000-0000-0000-000000000001",
  "data": {
    "mining": {
      "coal": 64,
      "copper": 0,
      "diamonds": 0,
      "glowstone": 0,
      "gold": 0,
      "iron": 40,
      "lapis": 0,
      "quartz": 0,
      "redstone": 0
    },
    "farming": {
      "breeding": 0,
      "carrots": 0,
      "hoe_used": 214,
      "melon": 0,
      "potatoes": 0,
      "pumpkin": 0,
      "sugar_cane": 101,
      "wheat": 185
    },
    "slayer": {
      "creeper": 0,
      "drowned": 0,
      "enderman": 0,
      "ender_dragon": 0,
      "hoglin": 0,
      "husk": 0,
      "magma_cube": 0,
      "phantom": 0,
      "piglin": 0,
      "pillager": 1,
      "silverfish": 0,
      "skeleton": 3,
      "slime": 0,
      "spider": 0,
      "vindicator": 0,
      "witch": 0,
      "wither": 0,
      "wither_skeleton": 0,
      "zombie": 4
    },
    "cooking": {
      "beef": 0,
      "bread": 43,
      "cake": 0,
      "chicken": 0,
      "cookie": 0,
      "fish": 0,
      "kelp": 0,
      "mutton": 0,
      "porkchop": 0,
      "potato": 0,
      "rabbit": 0,
      "stew": 0
    }
  }
}
```
