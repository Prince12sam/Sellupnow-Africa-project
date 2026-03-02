<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check static bootstrappers
$artisanRef = new ReflectionClass(Illuminate\Console\Application::class);
$bootstrappersProp = $artisanRef->getProperty('bootstrappers');
$bootstrappersProp->setAccessible(true);
$bootstrappers = $bootstrappersProp->getValue(null);
echo "Static bootstrappers count: " . count($bootstrappers) . PHP_EOL;

// Monkey-patch: override add() to track calls
// Can't override - instead, let's patch by checking what happens in detail

// Get the kernel's artisan via getArtisan()
$kernelRef = new ReflectionClass($kernel);
$getArtisanMethod = $kernelRef->getMethod('getArtisan');
$getArtisanMethod->setAccessible(true);
$artisan = $getArtisanMethod->invoke($kernel);

// Now check artisan's commandMap
$artisanClassRef = new ReflectionClass($artisan);
$commandMapProp = $artisanClassRef->getProperty('commandMap');
$commandMapProp->setAccessible(true);
$commandMap = $commandMapProp->getValue($artisan);
echo "commandMap has config:clear: " . (isset($commandMap['config:clear']) ? 'YES => ' . $commandMap['config:clear'] : 'NO') . PHP_EOL;

// Get the commands array from Symfony Application parent
$symfonyRef = new ReflectionClass(get_parent_class($artisan)); // Symfony\Component\Console\Application
$commandsProp = $symfonyRef->getProperty('commands');
$commandsProp->setAccessible(true);
$commands = $commandsProp->getValue($artisan);
echo "Symfony commands['config:clear'] exists: " . (isset($commands['config:clear']) ? 'YES => ' . get_class($commands['config:clear']) : 'NO') . PHP_EOL;

if (isset($commands['config:clear'])) {
    $cmd = $commands['config:clear'];
    $cmdRef = new ReflectionClass($cmd);
    while ($cmdRef && !$cmdRef->hasProperty('laravel')) {
        $cmdRef = $cmdRef->getParentClass();
    }
    if ($cmdRef) {
        $laravelProp = $cmdRef->getProperty('laravel');
        $laravelProp->setAccessible(true);
        echo "config:clear->laravel: " . ($laravelProp->getValue($cmd) ? get_class($laravelProp->getValue($cmd)) : 'NULL') . PHP_EOL;
    }
}
