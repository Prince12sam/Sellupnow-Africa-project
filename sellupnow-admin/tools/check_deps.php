<?php
$pkg = json_decode(file_get_contents(__DIR__ . '/../vendor/laravel/framework/composer.json'));
echo "Laravel requires symfony/console: " . ($pkg->require->{'symfony/console'} ?? 'NOT FOUND') . PHP_EOL;
echo "Installed laravel/framework: " . json_decode(file_get_contents(__DIR__ . '/../vendor/laravel/framework/composer.json'))->name . PHP_EOL;
