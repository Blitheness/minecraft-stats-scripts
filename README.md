# Minecraft Stats Scripts
## Overview
Scripts to parse Minecraft statistics and advancement JSON files, and send relevant information to an API endpoint.

## Requirements
- PHP 8.0
- PHP Ctype and intl extensions
- [Composer](https://getcomposer.org)

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