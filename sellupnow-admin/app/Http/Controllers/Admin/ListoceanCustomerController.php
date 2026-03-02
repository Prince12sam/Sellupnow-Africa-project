<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ListoceanCustomerController extends Controller
{
    private function listocean()
    {
        return DB::connection('listocean');
    }

    private function customerWebUrl(): string
    {
        return rtrim((string) env('CUSTOMER_WEB_URL', 'http://127.0.0.1:8090'), '/');
    }

    private function resolveThumbnail($imageValue): string
    {
        $customerWebUrl = $this->customerWebUrl();

        if (is_numeric($imageValue) && (int) $imageValue > 0) {
            $path = (string) ($this->listocean()->table('media_uploads')->where('id', (int) $imageValue)->value('path') ?? '');
            if ($path !== '') {
                return $customerWebUrl . '/assets/uploads/media-uploader/' . ltrim($path, '/');
            }
        }

        $image = (string) ($imageValue ?? '');
        if ($image !== '') {
            if (preg_match('~^https?://~i', $image)) {
                return $image;
            }
            return $customerWebUrl . '/' . ltrim($image, '/');
        }

        return asset('assets/icons/user.svg');
    }

    private function loadUserOrFail(int $id): object
    {
        $user = $this->listocean()->table('users')->where('id', $id)->whereNull('deleted_at')->first();
        abort_if(! $user, 404);

        $fullName = trim(trim((string) ($user->first_name ?? '')).' '.trim((string) ($user->last_name ?? '')));
        $fullName = $fullName !== '' ? $fullName : (string) ($user->username ?? 'User');

        $user->fullName = $fullName;
        $user->thumbnail = $this->resolveThumbnail($user->image ?? null);

        // Optional: membership/subscription (customer web)
        $user->activeSubscription = null;
        try {
            if ($this->listocean()->getSchemaBuilder()->hasTable('user_subscriptions')) {
                $user->activeSubscription = $this->listocean()->table('user_subscriptions')
                    ->where('user_id', $id)
                    ->where('status', 'active')
                    ->orderByDesc('id')
                    ->first();
            }
        } catch (\Throwable $th) {
            $user->activeSubscription = null;
        }

        return $user;
    }

    public function create(): View
    {
        return view('admin.listocean-customer.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'username' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'phone' => 'nullable|string|max:191',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $existsUsername = $this->listocean()->table('users')->where('username', $data['username'])->exists();
        if ($existsUsername) {
            return back()->withErrors(['username' => __('Username already exists')])->withInput();
        }

        $existsEmail = $this->listocean()->table('users')->where('email', $data['email'])->exists();
        if ($existsEmail) {
            return back()->withErrors(['email' => __('Email already exists')])->withInput();
        }

        $now = now();

        $id = (int) $this->listocean()->table('users')->insertGetId([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'image' => null,
            'email_verified' => 1,
            'email_verify_token' => null,
            'email_verified_at' => $now,
            'password_changed_at' => $now,
            'verified_status' => 0,
            'status' => 1,
            'remember_token' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return to_route('admin.siteCustomer.show', ['id' => $id])->withSuccess(__('User created successfully'));
    }

    public function show(int $id): View
    {
        $user = $this->loadUserOrFail($id);

        $membershipPlans = collect();
        try {
            if ($this->listocean()->getSchemaBuilder()->hasTable('membership_plans')) {
                $membershipPlans = $this->listocean()->table('membership_plans')
                    ->orderBy('id')
                    ->get(['id', 'name', 'duration_days', 'price', 'currency', 'is_active']);
            }
        } catch (\Throwable $th) {
            $membershipPlans = collect();
        }

        return view('admin.listocean-customer.show', compact('user', 'membershipPlans'));
    }

    public function edit(int $id): View
    {
        $user = $this->loadUserOrFail($id);

        return view('admin.listocean-customer.edit', compact('user'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $user = $this->loadUserOrFail($id);

        $data = $request->validate([
            'first_name' => 'required|string|max:191',
            'last_name' => 'required|string|max:191',
            'username' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'phone' => 'nullable|string|max:191',
            'status' => 'required|in:0,1',
        ]);

        $existsUsername = $this->listocean()->table('users')
            ->where('username', $data['username'])
            ->where('id', '!=', $id)
            ->exists();
        if ($existsUsername) {
            return back()->withErrors(['username' => __('Username already exists')])->withInput();
        }

        $existsEmail = $this->listocean()->table('users')
            ->where('email', $data['email'])
            ->where('id', '!=', $id)
            ->exists();
        if ($existsEmail) {
            return back()->withErrors(['email' => __('Email already exists')])->withInput();
        }

        $this->listocean()->table('users')->where('id', $id)->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'status' => (int) $data['status'],
            'updated_at' => now(),
        ]);

        return to_route('admin.siteCustomer.show', ['id' => $id])->withSuccess(__('User updated successfully'));
    }

    public function resetPassword(Request $request, int $id): RedirectResponse
    {
        $this->loadUserOrFail($id);

        $data = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $now = now();

        $this->listocean()->table('users')->where('id', $id)->update([
            'password' => Hash::make($data['password']),
            'password_changed_at' => $now,
            'updated_at' => $now,
        ]);

        return back()->withSuccess(__('Password updated successfully'));
    }

    public function updateSubscription(Request $request, int $id): RedirectResponse
    {
        $this->loadUserOrFail($id);

        try {
            if (! $this->listocean()->getSchemaBuilder()->hasTable('user_subscriptions')) {
                return back()->withErrors([
                    'subscription' => __('Subscription table is missing on customer web. Run the customer web migration for user_subscriptions first.'),
                ]);
            }
        } catch (\Throwable $th) {
            return back()->withErrors(['subscription' => $th->getMessage()]);
        }

        $data = $request->validate([
            'membership_plan_id' => 'nullable|integer|min:1',
            'subscription_name' => 'nullable|string|max:191',
            'start_date' => 'nullable|date',
            'expire_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive,canceled',
        ]);

        $subscriptionName = trim((string) ($data['subscription_name'] ?? ''));
        $startDate = $data['start_date'] ?? null;
        $expireDate = $data['expire_date'] ?? null;

        if (! empty($data['membership_plan_id'])) {
            try {
                if (! $this->listocean()->getSchemaBuilder()->hasTable('membership_plans')) {
                    return back()->withErrors([
                        'subscription' => __('Membership plans table is missing on customer web. Run the customer web migration for membership_plans first.'),
                    ])->withInput();
                }
            } catch (\Throwable $th) {
                return back()->withErrors(['subscription' => $th->getMessage()])->withInput();
            }

            $plan = $this->listocean()->table('membership_plans')->where('id', (int) $data['membership_plan_id'])->first();
            if (! $plan) {
                return back()->withErrors(['membership_plan_id' => __('Selected plan not found')])->withInput();
            }

            $subscriptionName = (string) ($plan->name ?? '');
            if ($subscriptionName === '') {
                return back()->withErrors(['membership_plan_id' => __('Selected plan is invalid')])->withInput();
            }

            $startDate = $startDate ?: now()->toDateString();
            if (! $expireDate) {
                $durationDays = (int) ($plan->duration_days ?? 0);
                if ($durationDays > 0) {
                    $expireDate = Carbon::parse($startDate)->addDays($durationDays)->toDateString();
                }
            }
        }

        if ($subscriptionName === '') {
            return back()->withErrors(['subscription_name' => __('Subscription name is required')])->withInput();
        }

        // Deactivate previous active subscriptions if setting a new active one.
        if ($data['status'] === 'active') {
            $this->listocean()->table('user_subscriptions')
                ->where('user_id', $id)
                ->where('status', 'active')
                ->update([
                    'status' => 'inactive',
                    'updated_at' => now(),
                ]);
        }

        $this->listocean()->table('user_subscriptions')->insert([
            'user_id' => $id,
            'subscription_name' => $subscriptionName,
            'start_date' => $startDate,
            'expire_date' => $expireDate,
            'status' => $data['status'],
            'created_by' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->withSuccess(__('Subscription updated successfully'));
    }
}
