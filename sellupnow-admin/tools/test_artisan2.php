<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Force kernel to create artisan via reflection
$kernelRef = new ReflectionClass($kernel);
$getArtisanMethod = $kernelRef->getMethod('getArtisan');
$getArtisanMethod->setAccessible(true);
$artisan = $getArtisanMethod->invoke($kernel);

echo 'Artisan instance: ' . get_class($artisan) . PHP_EOL;
echo 'Artisan laravel: ' . get_class($artisan->getLaravel()) . PHP_EOL;

$all = $artisan->all();
echo "Total commands: " . count($all) . PHP_EOL;
echo "config:clear pre-loaded: " . (isset($all['config:clear']) ? 'YES' : 'NO') . PHP_EOL;

// Check command loader
$parentRef = new ReflectionClass(get_parent_class($artisan));
if ($parentRef->hasProperty('commandLoader')) {
    $loaderProp = $parentRef->getProperty('commandLoader');
    $loaderProp->setAccessible(true);
    $loader = $loaderProp->getValue($artisan);
    echo 'Command loader: ' . ($loader ? get_class($loader) : 'NULL') . PHP_EOL;
    if ($loader) {
        echo "Loader has config:clear: " . ($loader->has('config:clear') ? 'YES' : 'NO') . PHP_EOL;
    }
}

// Find the command and check its laravel property
try {
    $cmd = $artisan->find('config:clear');
    echo "Found command class: " . get_class($cmd) . PHP_EOL;
    $cmdRef = new ReflectionClass($cmd);
    $laravelProp = null;
    while ($cmdRef) {
        if ($cmdRef->hasProperty('laravel')) {
            $laravelProp = $cmdRef->getProperty('laravel');
            $laravelProp->setAccessible(true);
            break;
        }
        $cmdRef = $cmdRef->getParentClass();
    }
    if ($laravelProp) {
        $laravelVal = $laravelProp->getValue($cmd);
        echo "Command->laravel: " . ($laravelVal ? get_class($laravelVal) : 'NULL') . PHP_EOL;
    } else {
        echo "Command has no laravel property" . PHP_EOL;
    }
} catch (Throwable $e) {
    echo "Error finding config:clear: " . $e->getMessage() . PHP_EOL;
}
