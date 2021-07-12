# Minecraft Stats Scripts
## Overview
Scripts to parse Minecraft statistics and advancement JSON files, and send relevant information to an API endpoint.

## Requirements
- PHP 8.0
- PHP Ctype and intl extensions

## Usage
```
php index.php --working-directory="/home/minecraft" --world-name="world"
```
or (short-hand arguments):
```
php index.php -d "/home/minecraft" -w "world"
```

The working directory argument is optional. It will default to the directory where the script is located.