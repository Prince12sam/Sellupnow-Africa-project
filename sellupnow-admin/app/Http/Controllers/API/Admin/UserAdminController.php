<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserNonPermission;
use App\Repositories\WalletRepository;
use App\Services\ListOceanAdminAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserAdminController extends Controller
{
    public function __construct(private readonly ListOceanAdminAdapter $listOceanAdminAdapter) {}

    public function index(Request $request)
    {
        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward($request, 'GET', '/users');
        }

        $page = max((int) $request->query('page', 1), 1);
        $perPage = max((int) $request->query('per_page', 20), 1);
        $skip = ($page - 1) * $perPage;

        $query = User::query()
            ->with('roles')
            ->whereHas('roles')
            ->when($request->filled('search'), function ($builder) use ($request) {
                $search = $request->query('search');
                $builder->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%');
                });
            })
            ->when($request->filled('role'), function ($builder) use ($request) {
                $builder->whereHas('roles', function ($q) use ($request) {
                    $q->where('name', $request->query('role'));
                });
            })
            ->latest('id');

        $total = $query->count();
        $users = $query->skip($skip)->take($perPage)->get();

        return $this->json('admin users', [
            'total' => $total,
            'users' => $users,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'nullable', 'email', Rule::unique('users', 'email')->ignore($id)],
            'phone' => ['sometimes', 'nullable', 'string', Rule::unique('users', 'phone')->ignore($id)],
            'is_active' => 'sometimes|boolean',
        ]);

        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward($request, 'PUT', '/users/'.$id, $data);
        }

        $user = User::query()->findOrFail($id);
        $user->update($data);

        return $this->json('user updated successfully', [
            'user' => $user->fresh('roles'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', Rule::unique('users', 'phone')],
            'password' => 'nullable|string|min:6',
            'role' => 'required|string|exists:roles,name',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward($request, 'POST', '/users', $data);
        }

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'password' => Hash::make($data['password'] ?? $data['phone']),
            'is_active' => $data['is_active'] ?? true,
        ]);

        $role = Role::query()->where('name', $data['role'])->firstOrFail();
        $user->assignRole($role);
        WalletRepository::storeByRequest($user);

        return $this->json('user created successfully', [
            'user' => $user->fresh('roles'),
        ]);
    }

    public function resetPassword(Request $request, int $id)
    {
        $data = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward($request, 'POST', '/users/'.$id.'/reset-password', $data);
        }

        $user = User::query()->findOrFail($id);
        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return $this->json('password updated successfully');
    }

    public function updatePermissions(Request $request, int $id)
    {
        $data = $request->validate([
            'role_id' => 'required|integer|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|max:255',
        ]);

        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward($request, 'POST', '/users/'.$id.'/permissions', $data);
        }

        $user = User::query()->findOrFail($id);
        $role = Role::query()->findOrFail($data['role_id']);

        $user->syncRoles([$role]);

        $rolePermissions = $role->getPermissionNames()->toArray();
        $permissions = $data['permissions'] ?? [];

        $customPermissions = [];
        $removePermissions = [];

        foreach ($permissions as $permission) {
            if (!in_array($permission, $rolePermissions, true)) {
                $customPermissions[] = $permission;
            }
        }

        foreach ($rolePermissions as $permission) {
            if (!in_array($permission, $permissions, true)) {
                $removePermissions[] = $permission;
            }
        }

        $user->syncPermissions($customPermissions);

        UserNonPermission::query()->where('user_id', $user->id)->delete();
        foreach ($removePermissions as $permission) {
            UserNonPermission::query()->create([
                'user_id' => $user->id,
                'name' => $permission,
            ]);
        }

        Cache::forget('user_permissions_'.$user->id);
        Cache::forget('user_non_permissions_'.$user->id);

        return $this->json('user permissions updated successfully', [
            'user' => $user->fresh('roles', 'permissions'),
        ]);
    }

    public function destroy(int $id)
    {
        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward(request(), 'DELETE', '/users/'.$id);
        }

        $user = User::query()->findOrFail($id);

        if ($user->hasRole('root')) {
            return $this->json('You can not delete root user', [], 422);
        }

        $user->syncRoles([]);
        $user->syncPermissions([]);
        $user->wallet()?->delete();
        $user->forceDelete();

        return $this->json('user deleted successfully');
    }
}
