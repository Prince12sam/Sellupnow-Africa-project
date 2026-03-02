<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
echo get_class($app) . PHP_EOL;
try {
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    echo "Kernel: " . get_class($kernel) . PHP_EOL;
    $kernel->bootstrap();
    echo "Bootstrap OK" . PHP_EOL;
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
    echo "Trace:\n" . $e->getTraceAsString() . PHP_EOL;
}
