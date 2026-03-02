<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Replicate exactly what artisan does
$input = new Symfony\Component\Console\Input\ArgvInput(['artisan', 'config:clear']);
$output = new Symfony\Component\Console\Output\ConsoleOutput;

try {
    $status = $kernel->handle($input, $output);
    echo "Exit status: $status" . PHP_EOL;
} catch (Throwable $e) {
    echo "CAUGHT: " . $e->getMessage() . PHP_EOL;
    echo "File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
    // Walk up call stack to find origin
    foreach ($e->getTrace() as $i => $frame) {
        echo "#$i " . ($frame['class'] ?? '') . ($frame['type'] ?? '') . ($frame['function'] ?? '') . 
             " @ " . ($frame['file'] ?? '?') . ":" . ($frame['line'] ?? '?') . PHP_EOL;
        if ($i > 20) { echo "...\n"; break; }
    }
}
