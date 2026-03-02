<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check static_options columns
$cols = DB::connection('listocean')->select("SHOW COLUMNS FROM static_options LIMIT 5");
echo "static_options columns:\n";
foreach($cols as $c) {
    echo "  " . $c->Field . " (" . $c->Type . ")\n";
}

// Terms content
$terms = DB::connection('listocean')->table('pages')->where('slug','terms-and-conditions')->first();
echo "\nterms-and-conditions page_content:\n" . ($terms->page_content ?? '(null)') . "\n";

// Safety tips - look at first 3 rows of static_options to find the field name
$rows = DB::connection('listocean')->table('static_options')->take(10)->get();
echo "\nSample static_options rows:\n";
foreach($rows as $r) {
    echo "  " . json_encode($r) . "\n";
}
