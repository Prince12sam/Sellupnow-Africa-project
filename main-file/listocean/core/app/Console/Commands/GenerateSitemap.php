<?php

namespace App\Console\Commands;

use App\Models\Backend\Category;
use App\Models\Backend\Listing;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateSitemap extends Command
{
    protected $signature   = 'sitemap:generate';
    protected $description = 'Generate the public XML sitemap for listings, categories, and seller profiles';

    public function handle(): int
    {
        $this->info('Generating sitemap...');

        $lines   = [];
        $lines[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $lines[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // ── Homepage ─────────────────────────────────────────────────────────
        $lines[] = $this->urlEntry(url('/'), 'daily', '1.0');

        // ── Active listings ──────────────────────────────────────────────────
        Listing::where('status', 1)
            ->where('is_published', 1)
            ->select(['slug', 'updated_at'])
            ->orderByDesc('updated_at')
            ->chunk(500, function ($listings) use (&$lines) {
                foreach ($listings as $listing) {
                    try {
                        $loc = route('frontend.listing.details', $listing->slug);
                    } catch (\Exception $e) {
                        $loc = url('/listing/' . $listing->slug);
                    }
                    $lines[] = $this->urlEntry(
                        $loc,
                        'weekly',
                        '0.8',
                        optional($listing->updated_at)->toAtomString()
                    );
                }
            });

        // ── Categories ───────────────────────────────────────────────────────
        Category::select(['slug', 'updated_at'])
            ->chunk(200, function ($cats) use (&$lines) {
                foreach ($cats as $cat) {
                    try {
                        $loc = route('frontend.show.listing.by.category', $cat->slug);
                    } catch (\Exception $e) {
                        $loc = url('/category/' . $cat->slug);
                    }
                    $lines[] = $this->urlEntry(
                        $loc,
                        'weekly',
                        '0.7',
                        optional($cat->updated_at)->toAtomString()
                    );
                }
            });

        // ── Seller profiles ──────────────────────────────────────────────────
        User::where('status', 1)
            ->select(['username', 'updated_at'])
            ->chunk(500, function ($users) use (&$lines) {
                foreach ($users as $user) {
                    if (empty($user->username)) {
                        continue;
                    }
                    try {
                        $loc = route('about.user.profile', $user->username);
                    } catch (\Exception $e) {
                        $loc = url('/seller/' . $user->username);
                    }
                    $lines[] = $this->urlEntry(
                        $loc,
                        'monthly',
                        '0.5',
                        optional($user->updated_at)->toAtomString()
                    );
                }
            });

        $lines[] = '</urlset>';

        $xml  = implode("\n", $lines);
        $path = public_path('sitemap.xml');

        file_put_contents($path, $xml);

        $this->info('Sitemap written → ' . $path);
        $this->info('Total URLs: ' . (substr_count($xml, '<url>')) . ' entries');

        return self::SUCCESS;
    }

    // ────────────────────────────────────────────────────────────────────────
    private function urlEntry(
        string  $loc,
        string  $changefreq = 'weekly',
        string  $priority   = '0.5',
        ?string $lastmod    = null
    ): string {
        $loc  = htmlspecialchars($loc, ENT_XML1 | ENT_COMPAT, 'UTF-8');
        $body = "  <url>\n    <loc>{$loc}</loc>\n";
        if ($lastmod) {
            $body .= "    <lastmod>{$lastmod}</lastmod>\n";
        }
        $body .= "    <changefreq>{$changefreq}</changefreq>\n";
        $body .= "    <priority>{$priority}</priority>\n";
        $body .= "  </url>";
        return $body;
    }
}
