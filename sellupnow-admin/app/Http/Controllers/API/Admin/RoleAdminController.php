<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Services\ListOceanAdminAdapter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleAdminController extends Controller
{
    public function __construct(private readonly ListOceanAdminAdapter $listOceanAdminAdapter) {}

    public function index(Request $request)
    {
        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward($request, 'GET', '/roles');
        }

        $query = Role::query()
            ->with('permissions')
            ->when($request->filled('search'), fn ($builder) => $builder->where('name', 'like', '%'.$request->query('search').'%'))
            ->orderByDesc('id');

        return $this->json('admin roles', [
            'roles' => $query->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')],
            'guard_name' => 'sometimes|string|max:50',
            'is_shop' => 'sometimes|boolean',
        ]);

        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward($request, 'POST', '/roles', $data);
        }

        $role = Role::query()->create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
            'is_shop' => $data['is_shop'] ?? false,
        ]);

        return $this->json('role created successfully', [
            'role' => $role,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($id)],
            'is_shop' => 'sometimes|boolean',
        ]);

        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward($request, 'PUT', '/roles/'.$id, $data);
        }

        $role = Role::query()->findOrFail($id);
        $role->update($data);

        return $this->json('role updated successfully', [
            'role' => $role,
        ]);
    }

    public function updatePermissions(Request $request, int $id)
    {
        $data = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|max:255',
        ]);

        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward($request, 'POST', '/roles/'.$id.'/permissions', $data);
        }

        $role = Role::query()->findOrFail($id);
        $role->syncPermissions($data['permissions'] ?? []);

        return $this->json('role permissions updated successfully', [
            'role' => $role->fresh('permissions'),
        ]);
    }

    public function destroy(int $id)
    {
        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward(request(), 'DELETE', '/roles/'.$id);
        }

        $role = Role::query()->findOrFail($id);
        if ($role->name === 'root') {
            return $this->json('You can not delete root role', [], 422);
        }

        $role->syncPermissions([]);
        $role->delete();

        return $this->json('role deleted successfully');
    }
}
