<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Category;

class ListoceanEscrowController extends Controller
{
    // All possible escrow statuses, human-readable labels + badge colour
    private array $statusMeta = [
        'payment_pending'  => ['label' => 'Payment Pending',   'class' => 'bg-secondary'],
        'funded'           => ['label' => 'Funded',             'class' => 'bg-info text-dark'],
        'seller_confirmed' => ['label' => 'Seller Accepted',    'class' => 'bg-primary'],
        'seller_delivered' => ['label' => 'Seller Delivered',   'class' => 'bg-warning text-dark'],
        'released'         => ['label' => 'Released',           'class' => 'bg-success'],
        'refunded'         => ['label' => 'Refunded',           'class' => 'bg-danger'],
        'disputed'         => ['label' => 'Disputed',           'class' => 'bg-danger'],
    ];

    public function index(Request $request)
    {
        $status      = (string) $request->get('status', 'all');
        $validStatuses = array_keys($this->statusMeta);

        $query = $this->listocean()
            ->table('escrow_transactions as e')
            ->leftJoin('listings as l', 'l.id', '=', 'e.listing_id')
            ->leftJoin('users as buyer', 'buyer.id', '=', 'e.buyer_user_id')
            ->leftJoin('users as seller', 'seller.id', '=', 'e.seller_user_id')
            ->select([
                'e.id',
                'e.listing_id',
                'e.buyer_user_id',
                'e.seller_user_id',
                'e.listing_price',
                'e.admin_fee_amount',
                'e.total_amount',
                'e.currency',
                'e.status',
                'e.payment_gateway',
                'e.payment_transaction_id',
                'e.funded_at',
                'e.seller_accepted_at',
                'e.seller_delivered_at',
                'e.buyer_confirmed_at',
                'e.released_at',
                'e.buyer_confirm_deadline_at',
                'e.seller_accept_deadline_at',
                'e.created_at',
                'l.title as listing_title',
            ])
            ->selectRaw("COALESCE(NULLIF(TRIM(COALESCE(buyer.first_name,'') || ' ' || COALESCE(buyer.last_name,'')), ''), buyer.username) as buyer_name")
            ->selectRaw("COALESCE(NULLIF(TRIM(COALESCE(seller.first_name,'') || ' ' || COALESCE(seller.last_name,'')), ''), seller.username) as seller_name")
            ->when($status !== 'all' && in_array($status, $validStatuses, true), fn ($b) => $b->where('e.status', $status))
            ->when($request->filled('search'), function ($b) use ($request) {
                $search = (string) $request->search;
                $b->where(function ($q) use ($search) {
                    $q->where('e.id', 'like', "%{$search}%")
                      ->orWhere('l.title', 'like', "%{$search}%")
                      ->orWhere('e.payment_transaction_id', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('e.id');

        try {
            $transactions = $query->paginate(20);
        } catch (\Throwable $e) {
            Log::error('Listocean escrow query failed', ['error' => $e->getMessage()]);
            $current = (int) ($request->get('page', 1) ?: 1);
            $transactions = new LengthAwarePaginator([], 0, 20, $current, ['path' => $request->url(), 'query' => $request->query()]);
        }

        $statusMeta = $this->statusMeta;

        return view('admin.escrow.index', compact('transactions', 'status', 'statusMeta'));
    }

    public function show(int $id)
    {
        $row = $this->listocean()
            ->table('escrow_transactions as e')
            ->leftJoin('listings as l', 'l.id', '=', 'e.listing_id')
            ->leftJoin('users as buyer', 'buyer.id', '=', 'e.buyer_user_id')
            ->leftJoin('users as seller', 'seller.id', '=', 'e.seller_user_id')
            ->select([
                'e.*',
                'l.title as listing_title',
                'l.price as listing_db_price',
                'l.slug as listing_slug',
            ])
            ->selectRaw("COALESCE(NULLIF(TRIM(COALESCE(buyer.first_name,'') || ' ' || COALESCE(buyer.last_name,'')), ''), buyer.username) as buyer_name")
            ->selectRaw("buyer.email as buyer_email")
            ->selectRaw("buyer.phone as buyer_phone")
            ->selectRaw("COALESCE(NULLIF(TRIM(COALESCE(seller.first_name,'') || ' ' || COALESCE(seller.last_name,'')), ''), seller.username) as seller_name")
            ->selectRaw("seller.email as seller_email")
            ->selectRaw("seller.phone as seller_phone")
            ->where('e.id', $id)
            ->first();

        if (! $row) abort(404);

        // Timeline events
        $events = $this->listocean()
            ->table('escrow_events as ev')
            ->leftJoin('users as u', 'u.id', '=', 'ev.actor_user_id')
            ->select([
                'ev.id',
                'ev.event',
                'ev.actor_type',
                'ev.from_status',
                'ev.to_status',
                'ev.note',
                'ev.created_at',
            ])
            ->selectRaw("COALESCE(NULLIF(TRIM(COALESCE(u.first_name,'') || ' ' || COALESCE(u.last_name,'')), ''), u.username) as actor_name")
            ->where('ev.escrow_transaction_id', $id)
            ->orderBy('ev.id')
            ->get();

        $statusMeta = $this->statusMeta;

        return view('admin.escrow.show', compact('row', 'events', 'statusMeta'));
    }

    /**
     * Show escrow configuration settings (reads values from Listocean static_options).
     */
    public function settings(Request $request)
    {
        $conn = $this->listocean();
        $keys = [
            'escrow_enabled',
            'escrow_fee_percent',
            'escrow_min_price',
            'escrow_max_price',
            'escrow_seller_accept_hours',
            'escrow_buyer_confirm_hours',
            'escrow_currency',
            'escrow_included_category_ids',
            'escrow_excluded_category_ids',
        ];

        $rows = $conn->table('static_options')->whereIn('option_name', $keys)->get()->pluck('option_value', 'option_name')->toArray();

        $options = [];
        $options['enabled'] = (!empty($rows['escrow_enabled']) && (string)$rows['escrow_enabled'] === 'on');
        $options['fee_percent'] = $rows['escrow_fee_percent'] ?? '2.5';
        $options['min_price'] = $rows['escrow_min_price'] ?? '0';
        $options['max_price'] = $rows['escrow_max_price'] ?? '999999999';
        $options['seller_accept_hours'] = $rows['escrow_seller_accept_hours'] ?? '24';
        $options['buyer_confirm_hours'] = $rows['escrow_buyer_confirm_hours'] ?? '72';
        $options['currency'] = $rows['escrow_currency'] ?? 'GHS';
        $options['included_category_ids'] = array_map('strval', json_decode($rows['escrow_included_category_ids'] ?? '[]', true) ?: []);
        $options['excluded_category_ids'] = array_map('strval', json_decode($rows['escrow_excluded_category_ids'] ?? '[]', true) ?: []);

        // Load available categories for selection in admin UI from the Listocean connection
        // Use the same DB connection that stores the listings/categories/static_options
        $categories = $this->listocean()->table('categories')->orderBy('name')->get(['id', 'name'])
            ->map(function ($c) {
                // ensure id/name are accessible and id is string for consistent comparison in the view
                return (object) ['id' => (string) ($c->id ?? $c['id'] ?? ''), 'name' => $c->name ?? ($c['name'] ?? '')];
            });

        return view('admin.escrow.settings', compact('options', 'categories'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'enabled' => 'nullable|boolean',
            'fee_percent' => 'required|numeric|min:0|max:50',
            'min_price' => 'required|numeric|min:0',
            'max_price' => 'required|numeric|min:0',
            'seller_accept_hours' => 'required|integer|min:1|max:720',
            'buyer_confirm_hours' => 'required|integer|min:1|max:720',
            'currency' => 'required|string|max:10',
            // included/excluded may be sent as array (multi-select) or comma-separated string
            'included_category_ids' => 'nullable',
            'excluded_category_ids' => 'nullable',
            // CSV fallbacks when admin uses the text inputs
            'included_category_ids_csv' => 'nullable|string',
            'excluded_category_ids_csv' => 'nullable|string',
        ]);

        try {
            $enabled = (bool) ($validated['enabled'] ?? false);

            // Parse included/excluded category id lists.
            // Priority: multi-select array inputs (`included_category_ids[]`) -> CSV text inputs (`included_category_ids_csv`) -> validated scalar `included_category_ids`.
            $included = [];
            $excluded = [];

            $incInput = $request->input('included_category_ids');
            if (is_array($incInput)) {
                $included = array_values(array_map('intval', $incInput));
            } else {
                $incCsv = trim((string) ($request->input('included_category_ids_csv', (string) ($validated['included_category_ids'] ?? ''))));
                $included = array_values(array_filter(array_map('intval', array_map('trim', $incCsv === '' ? [] : explode(',', $incCsv)))));
            }

            $excInput = $request->input('excluded_category_ids');
            if (is_array($excInput)) {
                $excluded = array_values(array_map('intval', $excInput));
            } else {
                $excCsv = trim((string) ($request->input('excluded_category_ids_csv', (string) ($validated['excluded_category_ids'] ?? ''))));
                $excluded = array_values(array_filter(array_map('intval', array_map('trim', $excCsv === '' ? [] : explode(',', $excCsv)))));
            }

            // Remove duplicates and normalize to integers
            $included = array_values(array_unique(array_filter($included, fn($v) => $v > 0)));
            $excluded = array_values(array_unique(array_filter($excluded, fn($v) => $v > 0)));

            // Security: limit the number of category IDs to prevent abuse
            $maxIds = 200;
            if (count($included) > $maxIds || count($excluded) > $maxIds) {
                return back()->with('error', 'Too many category IDs submitted. Reduce the number and try again.');
            }

            // Verify the provided category IDs exist in the Listocean categories table (using listocean connection)
            $existingIds = $this->listocean()->table('categories')
                ->whereIn('id', array_merge($included, $excluded))
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->toArray();

            // Keep only IDs that exist
            $included = array_values(array_intersect($included, $existingIds));
            $excluded = array_values(array_intersect($excluded, $existingIds));

            // Ensure no overlap: if an ID is in both, prefer included and remove from excluded
            $excluded = array_values(array_diff($excluded, $included));

            $conn = $this->listocean();
            $now = now();

            $upsert = function(string $name, string $value) use ($conn, $now) {
                $conn->table('static_options')->updateOrInsert(
                    ['option_name' => $name],
                    ['option_value' => $value, 'updated_at' => $now, 'created_at' => $now]
                );
            };

            $upsert('escrow_enabled', $enabled ? 'on' : 'off');
            $upsert('escrow_fee_percent', (string) (float) $validated['fee_percent']);
            $upsert('escrow_min_price', (string) (float) $validated['min_price']);
            $upsert('escrow_max_price', (string) (float) $validated['max_price']);
            $upsert('escrow_seller_accept_hours', (string) (int) $validated['seller_accept_hours']);
            $upsert('escrow_buyer_confirm_hours', (string) (int) $validated['buyer_confirm_hours']);
            $upsert('escrow_currency', strtoupper(trim((string) $validated['currency'])));
            $upsert('escrow_included_category_ids', json_encode($included));
            $upsert('escrow_excluded_category_ids', json_encode($excluded));

            return back()->withSuccess(__('Escrow settings updated successfully'));
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Admin manually releases escrow funds to the seller's wallet.
     * Use when buyer_confirm_deadline has passed or a dispute was resolved in seller's favour.
     */
    public function adminRelease(Request $request, int $id)
    {
        $request->validate(['note' => 'nullable|string|max:512']);

        $row = $this->listocean()->table('escrow_transactions')->where('id', $id)->first();
        if (! $row) return back()->withError('Escrow transaction not found.');

        $allowedStatuses = ['funded', 'seller_confirmed', 'seller_delivered', 'disputed'];
        if (! in_array((string) $row->status, $allowedStatuses, true)) {
            return back()->withError('Cannot release: escrow is in status "' . $row->status . '".');
        }

        try {
            $this->listocean()->transaction(function () use ($row, $id, $request) {
                // Credit seller wallet
                $wallet = $this->listocean()->table('wallets')->where('user_id', $row->seller_user_id)->first();
                if ($wallet) {
                    $this->listocean()->table('wallets')
                        ->where('user_id', $row->seller_user_id)
                        ->update(['balance' => (float) $wallet->balance + (float) $row->listing_price]);
                } else {
                    $this->listocean()->table('wallets')->insert([
                        'user_id'    => $row->seller_user_id,
                        'balance'    => (float) $row->listing_price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                $this->listocean()->table('wallet_histories')->insert([
                    'user_id'          => $row->seller_user_id,
                    'type'             => 'escrow_release',
                    'amount'           => (float) $row->listing_price,
                    'payment_gateway'  => 'escrow',
                    'payment_status'   => 'complete',
                    'metadata'         => json_encode(['note' => 'Admin manual release — escrow #' . $id . '. ' . ($request->input('note') ?? '')]),
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                // Update escrow status
                $this->listocean()->table('escrow_transactions')->where('id', $id)->update([
                    'status'      => 'released',
                    'released_at' => now(),
                    'updated_at'  => now(),
                ]);

                // Log event
                $this->listocean()->table('escrow_events')->insert([
                    'escrow_transaction_id' => $id,
                    'actor_user_id'         => null,
                    'actor_type'            => 'admin',
                    'event'                 => 'admin_release',
                    'from_status'           => (string) $row->status,
                    'to_status'             => 'released',
                    'note'                  => $request->input('note') ?? 'Manual admin release',
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Escrow admin release failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->withError('Release failed: ' . $e->getMessage());
        }

        return back()->withSuccess('Funds released to seller wallet.');
    }

    /**
     * Admin manually refunds buyer — marks escrow as refunded.
     * The actual Paystack refund must be processed via the Paystack dashboard.
     */
    public function adminRefund(Request $request, int $id)
    {
        $request->validate(['note' => 'nullable|string|max:512']);

        $row = $this->listocean()->table('escrow_transactions')->where('id', $id)->first();
        if (! $row) return back()->withError('Escrow transaction not found.');

        $allowedStatuses = ['funded', 'seller_confirmed', 'seller_delivered', 'disputed'];
        if (! in_array((string) $row->status, $allowedStatuses, true)) {
            return back()->withError('Cannot refund: escrow is in status "' . $row->status . '".');
        }

        try {
            $this->listocean()->transaction(function () use ($row, $id, $request) {
                $this->listocean()->table('escrow_transactions')->where('id', $id)->update([
                    'status'     => 'refunded',
                    'updated_at' => now(),
                ]);

                $this->listocean()->table('escrow_events')->insert([
                    'escrow_transaction_id' => $id,
                    'actor_user_id'         => null,
                    'actor_type'            => 'admin',
                    'event'                 => 'admin_refund',
                    'from_status'           => (string) $row->status,
                    'to_status'             => 'refunded',
                    'note'                  => $request->input('note') ?? 'Manual admin refund',
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Escrow admin refund failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->withError('Refund failed: ' . $e->getMessage());
        }

        return back()->withSuccess('Escrow marked as refunded. Process the Paystack refund via your Paystack dashboard.');
    }

    /**
     * Admin flags an escrow as disputed for investigation.
     */
    public function adminDispute(Request $request, int $id)
    {
        $request->validate(['note' => 'nullable|string|max:512']);

        $row = $this->listocean()->table('escrow_transactions')->where('id', $id)->first();
        if (! $row) return back()->withError('Escrow transaction not found.');

        if (in_array((string) $row->status, ['released', 'refunded'], true)) {
            return back()->withError('Cannot dispute a closed escrow.');
        }

        try {
            $this->listocean()->transaction(function () use ($row, $id, $request) {
                $this->listocean()->table('escrow_transactions')->where('id', $id)->update([
                    'status'     => 'disputed',
                    'updated_at' => now(),
                ]);

                $this->listocean()->table('escrow_events')->insert([
                    'escrow_transaction_id' => $id,
                    'actor_user_id'         => null,
                    'actor_type'            => 'admin',
                    'event'                 => 'admin_dispute_flagged',
                    'from_status'           => (string) $row->status,
                    'to_status'             => 'disputed',
                    'note'                  => $request->input('note') ?? 'Flagged for dispute by admin',
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Escrow admin dispute flag failed', ['id' => $id, 'error' => $e->getMessage()]);
            return back()->withError('Failed: ' . $e->getMessage());
        }

        return back()->withSuccess('Escrow flagged as disputed.');
    }

    private function listocean()
    {
        return DB::connection('listocean');
    }
}
