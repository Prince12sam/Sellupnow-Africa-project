<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ApiSmokeAdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('email', 'apitest@example.com')->first();
        if (! $user) {
            return;
        }

        $role = Role::findOrCreate('root', config('auth.defaults.guard', 'web'));
        if (! $user->hasRole($role->name)) {
            $user->assignRole($role->name);
        }
    }
}
