<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageContentSeeder extends Seeder
{
    /**
     * Seed default page content into the listocean frontend DB.
     * Only fills pages that are empty or have placeholder content (<= 200 chars).
     */
    public function run(): void
    {
        // ── About Us ─────────────────────────────────────────────────────────
        $this->seedPage('about', '<h2>About SellUpNow</h2>
<p>Welcome to <strong>SellUpNow</strong> — the smarter way to buy and sell pre-owned items in your local community.</p>
<p>We built SellUpNow to make second-hand selling as simple as snapping a photo. Whether you\'re clearing out your home or hunting for a great deal, our platform connects buyers and sellers quickly, safely, and conveniently.</p>
<h3>Our Mission</h3>
<p>To create a trusted marketplace where everyone can find value — reducing waste and making quality goods accessible to all.</p>
<h3>Why SellUpNow?</h3>
<ul>
  <li>Easy listing in under a minute</li>
  <li>Verified sellers and buyer protection</li>
  <li>Local &amp; national listings</li>
  <li>Secure in-app messaging</li>
  <li>Dedicated customer support</li>
</ul>
<p>Have questions? Visit our <a href="/contact">Contact page</a> or browse our <a href="/faq">FAQ</a>.</p>');

        // ── Terms & Conditions ────────────────────────────────────────────────
        // Only overwrite if content is essentially a stub (<= 200 chars)
        $this->seedPage('terms-and-conditions', '<h2>Terms &amp; Conditions</h2>
<p><em>Last updated: ' . now()->format('F j, Y') . '</em></p>

<h3>1. Acceptance of Terms</h3>
<p>By accessing or using the SellUpNow platform ("Service"), you agree to be bound by these Terms &amp; Conditions. If you do not agree, please do not use the Service.</p>

<h3>2. User Accounts</h3>
<p>You are responsible for maintaining the confidentiality of your account credentials and for all activities that occur under your account. You must be at least 18 years old to register.</p>

<h3>3. Listings &amp; Transactions</h3>
<p>Sellers must accurately describe items and have the legal right to sell them. SellUpNow acts solely as a marketplace intermediary and is not party to any transaction between buyers and sellers.</p>

<h3>4. Prohibited Items</h3>
<p>You may not list illegal items, counterfeit goods, weapons, or any item that violates applicable law. SellUpNow reserves the right to remove any listing at its sole discretion.</p>

<h3>5. Payments</h3>
<p>Payment processing is handled by our third-party payment partners. SellUpNow does not store your full payment details. All fees are disclosed at checkout.</p>

<h3>6. Intellectual Property</h3>
<p>All content, trademarks, and software on the platform are the property of SellUpNow or its licensors. You may not reproduce or redistribute any content without written permission.</p>

<h3>7. Limitation of Liability</h3>
<p>SellUpNow is not liable for any indirect, incidental, or consequential damages arising from your use of the Service. Our total liability shall not exceed the fees paid by you in the 3 months preceding the claim.</p>

<h3>8. Modifications</h3>
<p>We may update these Terms from time to time. Continued use of the Service after changes constitutes acceptance of the new Terms.</p>

<h3>9. Contact</h3>
<p>For questions regarding these Terms, please contact us at <a href="/contact">our contact page</a>.</p>', 200);

        // ── FAQ static page ───────────────────────────────────────────────────
        // This is the HTML "page" for /faq route — just a container that the
        // app renders. Seed a minimal intro; actual Q&A items live in the faqs table.
        $this->seedPage('faq', '<h2>Frequently Asked Questions</h2>
<p>Find answers to the most common questions about buying, selling, and using SellUpNow below.</p>
<p>Can\'t find what you\'re looking for? <a href="/contact">Contact our support team</a> — we\'re happy to help.</p>');

        // ── Safety Tips (static_options) ──────────────────────────────────────
        $this->seedStaticOption('safety_tips_info', '<h3>Stay Safe on SellUpNow</h3>
<ul>
  <li><strong>Meet in public</strong> — Choose busy, well-lit public places for in-person exchanges (e.g., shopping centres, cafés).</li>
  <li><strong>Bring a friend</strong> — Never meet a stranger alone for high-value transactions.</li>
  <li><strong>Inspect before you pay</strong> — Verify the item matches its description before completing payment.</li>
  <li><strong>Use secure payment</strong> — Always pay through the app. Never send cash transfers to strangers.</li>
  <li><strong>Protect your personal info</strong> — Only share contact details through our in-app messaging system.</li>
  <li><strong>Trust your instincts</strong> — If something feels wrong, walk away. Report suspicious behaviour using the in-app report button.</li>
  <li><strong>Check seller ratings</strong> — Review seller history and ratings before committing to a purchase.</li>
</ul>
<p>Remember: SellUpNow will never ask for your password or full payment details via message or email.</p>');

        $this->command->info('PageContentSeeder completed.');
    }

    /**
     * Seed page content only if the record is empty or a stub.
     *
     * @param  string  $slug
     * @param  string  $content
     * @param  int     $stubThreshold  pages with content <= this length are treated as stubs
     */
    private function seedPage(string $slug, string $content, int $stubThreshold = 0): void
    {
        $page = DB::connection('listocean')
            ->table('pages')
            ->where('slug', $slug)
            ->first();

        if (! $page) {
            $this->command->warn("Page [{$slug}] not found — skipping.");
            return;
        }

        $existing = trim($page->page_content ?? '');

        if (strlen($existing) > $stubThreshold) {
            $this->command->line("Page [{$slug}] already has content (" . strlen($existing) . " chars) — skipping.");
            return;
        }

        DB::connection('listocean')
            ->table('pages')
            ->where('slug', $slug)
            ->update(['page_content' => $content]);

        $this->command->info("Page [{$slug}] seeded (" . strlen($content) . " chars).");
    }

    /**
     * Seed a static_option value only if it is empty.
     */
    private function seedStaticOption(string $optionName, string $value): void
    {
        $existing = DB::connection('listocean')
            ->table('static_options')
            ->where('option_name', $optionName)
            ->value('option_value');

        if (! empty(trim($existing ?? ''))) {
            $this->command->line("Static option [{$optionName}] already has content — skipping.");
            return;
        }

        DB::connection('listocean')
            ->table('static_options')
            ->updateOrInsert(
                ['option_name' => $optionName],
                ['option_value' => $value]
            );

        $this->command->info("Static option [{$optionName}] seeded.");
    }
}
