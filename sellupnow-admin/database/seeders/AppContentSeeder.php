<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\IdProofType;
use App\Models\Tip;
use Illuminate\Database\Seeder;

class AppContentSeeder extends Seeder
{
    public function run(): void
    {
        // ── FAQs ─────────────────────────────────────────────────────────────
        if (Faq::count() === 0) {
            $faqs = [
                ['question' => 'How do I post an ad?',           'answer' => 'Tap the + button on the home screen, fill in the details and tap Submit.',            'sort_order' => 1],
                ['question' => 'How do I edit my listing?',      'answer' => 'Go to My Listings, select the listing and tap Edit.',                                  'sort_order' => 2],
                ['question' => 'How long does an ad stay live?', 'answer' => 'Ads remain active for 30 days and can be renewed from your dashboard.',                'sort_order' => 3],
                ['question' => 'How do I contact a seller?',     'answer' => 'Open the listing and tap the Call or Chat button.',                                     'sort_order' => 4],
                ['question' => 'Is it free to post an ad?',      'answer' => 'Basic listing is free. Premium packages are available for featured placement.',         'sort_order' => 5],
                ['question' => 'How do I delete my account?',    'answer' => 'Go to Profile → Settings → Deactivate Account.',                                       'sort_order' => 6],
                ['question' => 'How do I report a listing?',     'answer' => 'Open the listing, tap the ⋮ menu and choose Report.',                                  'sort_order' => 7],
            ];
            foreach ($faqs as $faq) {
                Faq::create($faq);
            }
        }

        // ── Tips ─────────────────────────────────────────────────────────────
        if (Tip::count() === 0) {
            $tips = [
                ['title' => 'Use clear photos',              'content' => 'Listings with high-quality photos get up to 3× more views.',                             'sort_order' => 1],
                ['title' => 'Write a detailed description',  'content' => 'Include brand, model, condition and any defects to build buyer trust.',                    'sort_order' => 2],
                ['title' => 'Set a fair price',              'content' => 'Research similar listings to price competitively.',                                        'sort_order' => 3],
                ['title' => 'Respond quickly',               'content' => 'Buyers often choose the first seller who responds.',                                       'sort_order' => 4],
                ['title' => 'Share your listing',            'content' => 'Share to social media for faster sales.',                                                  'sort_order' => 5],
                ['title' => 'Keep your info updated',        'content' => 'Ensure your contact number and location are current so buyers can reach you.',             'sort_order' => 6],
            ];
            foreach ($tips as $tip) {
                Tip::create($tip);
            }
        }

        // ── ID Proof Types ────────────────────────────────────────────────────
        if (IdProofType::count() === 0) {
            $types = [
                "National ID Card",
                "Passport",
                "Driver's License",
                "Voter's Card",
                "Utility Bill",
                "Bank Statement",
                "Work ID Card",
            ];
            foreach ($types as $i => $name) {
                IdProofType::create(['name' => $name, 'is_active' => true]);
            }
        }
    }
}
