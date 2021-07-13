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
  "adventure/sleep_in_bed": {
    "complete": true,
    "completed_at": {
      "date": "2021-07-03 16:54:09.000000",
      "timezone_type": 1,
      "timezone": "+00:00"
    }
  },
  "husbandry/tame_an_animal": {
    "complete": true,
    "completed_at": {
      "date": "2021-07-04 13:45:10.000000",
      "timezone_type": 1,
      "timezone": "+00:00"
    }
  },
  "husbandry/make_a_sign_glow": {
    "complete": true,
    "completed_at": {
      "date": "2021-07-05 09:13:38.000000",
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
  "mining": {
    "coal": 513,
    "copper": 14,
    "diamonds": 39,
    "glowstone": 30,
    "gold": 90,
    "iron": 335,
    "lapis": 19,
    "quartz": 245,
    "redstone": 238
  },
  "farming": {
    "breeding": 370,
    "carrots": 8,
    "hoe_used": 19,
    "melon": 1,
    "potatoes": 2,
    "pumpkin": 1,
    "sugar_cane": 567,
    "wheat": 1783
  },
  "slayer": {
    "creeper": 49,
    "drowned": 17,
    "enderman": 2412,
    "ender_dragon": 0,
    "hoglin": 0,
    "husk": 2,
    "magma_cube": 156,
    "phantom": 10,
    "piglin": 0,
    "pillager": 0,
    "silverfish": 0,
    "skeleton": 273,
    "slime": 0,
    "spider": 47,
    "vindicator": 0,
    "witch": 1,
    "wither": 0,
    "wither_skeleton": 9,
    "zombie": 128
  },
  "cooking": {
    "beef": 0,
    "bread": 63,
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
```
