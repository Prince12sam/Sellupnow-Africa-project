<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$slugs = ['about','terms-and-conditions','privacy-policy','contact','faq'];
foreach($slugs as $s) {
    $p = DB::connection('listocean')->table('pages')->where('slug',$s)->first();
    $status = $p
        ? 'exists, content=' . (strlen($p->page_content ?? '') > 0 ? strlen($p->page_content).' chars' : 'EMPTY')
        : 'MISSING';
    echo $s . ': ' . $status . "\n";
}

$st = DB::connection('listocean')->table('static_options')->where('key','safety_tips_info')->first();
echo "safety_tips_info: " . ($st ? 'exists, val=' . (strlen($st->value ?? '') > 0 ? strlen($st->value).' chars' : 'EMPTY') : 'MISSING') . "\n";
