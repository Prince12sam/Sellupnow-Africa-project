<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\ShopPasswordResetRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\CustomerRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index()
    {
        // Listocean frontend users live in a separate DB (connection: listocean).
        // This page is used as the canonical “Customers” list for Listocean.
        $customerWebUrl = rtrim((string) config('app.customer_web_url', ''), '/');

        $issiteCustomers = false;

        try {
            $customers = DB::connection('listocean')
                ->table('users')
                ->select(['id', 'first_name', 'last_name', 'username', 'email', 'phone', 'image', 'created_at'])
                ->whereNull('deleted_at')
                ->orderByDesc('id')
                ->paginate(20);

            $imageIds = $customers->getCollection()
                ->pluck('image')
                ->filter(fn ($v) => is_numeric($v) && (int) $v > 0)
                ->map(fn ($v) => (int) $v)
                ->unique()
                ->values()
                ->all();

            $mediaPathsById = [];
            if (!empty($imageIds)) {
                $mediaPathsById = DB::connection('listocean')
                    ->table('media_uploads')
                    ->whereIn('id', $imageIds)
                    ->pluck('path', 'id')
                    ->toArray();
            }

            $customers->setCollection(
                $customers->getCollection()->map(function ($user) use ($customerWebUrl, $mediaPathsById) {
                    $fullName = trim(trim((string) ($user->first_name ?? '')).' '.trim((string) ($user->last_name ?? '')));
                    $fullName = $fullName !== '' ? $fullName : (string) ($user->username ?? '');
                    $fullName = $fullName !== '' ? $fullName : 'User';

                    $thumbnail = '';

                    // Listocean often stores `users.image` as a numeric media_uploads id.
                    if (is_numeric($user->image) && (int) $user->image > 0) {
                        $imageId = (int) $user->image;
                        $path = (string) ($mediaPathsById[$imageId] ?? '');
                        if ($path !== '') {
                            $thumbnail = $customerWebUrl . '/assets/uploads/media-uploader/' . ltrim($path, '/');
                        }
                    } else {
                        // Some installs may store a relative path or a full URL.
                        $image = (string) ($user->image ?? '');
                        if ($image !== '') {
                            if (preg_match('~^https?://~i', $image)) {
                                $thumbnail = $image;
                            } else {
                                $thumbnail = $customerWebUrl !== '' ? ($customerWebUrl . '/' . ltrim($image, '/')) : $image;
                            }
                        }
                    }

                    $user->fullName = $fullName;
                    $user->thumbnail = $thumbnail !== '' ? $thumbnail : asset('assets/icons/user.svg');

                    // Fields that exist on Sellupnow users but not on Listocean users
                    $user->gender = $user->gender ?? null;
                    $user->date_of_birth = $user->date_of_birth ?? null;

                    return $user;
                })
            );

            $issiteCustomers = true;
        } catch (\Throwable $e) {
            // Fallback: show Sellupnow customers if Listocean connection isn't available.
            $customers = User::role(Roles::CUSTOMER->value)->latest('id')->with('media')->paginate(20);
        }

        return view('admin.customer.index', compact('customers', 'issiteCustomers'));
    }

    public function create()
    {
        return view('admin.customer.create');
    }

    public function store(RegistrationRequest $request)
    {
        // Create a new user
        $user = UserRepository::registerNewUser($request);

        // Create a new customer
        CustomerRepository::storeByRequest($user);

        // create wallet
        WalletRepository::storeByRequest($user);

        $user->assignRole(Roles::CUSTOMER->value);

        return to_route('admin.customer.index')->withSuccess(__('Created successfully'));
    }

    public function edit(User $customer)
    {
        return view('admin.customer.edit', compact('customer'));
    }

    public function update(User $customer, UserRequest $request)
    {
        UserRepository::updateByRequest($request, $customer);

        return to_route('admin.customer.index')->withSuccess(__('Updated successfully'));
    }

    public function destroy(User $customer)
    {
        $media = $customer->media;

        if ($media && Storage::exists($media->src)) {
            Storage::delete($media->src);
        }

        $customer->wallet()?->delete();
        $customer->syncPermissions([]);
        $customer->syncRoles([]);

        $delTime = now()->format('YmdHis');

        $customer->update([
            'phone' => $customer->phone.'_deleted:'.$delTime,
            'email' => $customer->email.'_deleted:'.$delTime,
            'deleted_at' => now(),
        ]);

        $media?->delete();

        return back()->withSuccess(__('Deleted successfully'));
    }

    public function resetPassword(User $customer, ShopPasswordResetRequest $request)
    {
        // Update the user password
        $customer->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->withSuccess(__('Password updated successfully'));
    }
}
