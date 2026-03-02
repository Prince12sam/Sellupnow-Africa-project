<?php

namespace App\Console\Commands;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class SellUpNowInstall extends Command
{
    protected $signature   = 'sellupnow:install
                              {--force : Skip all confirmation prompts}
                              {--no-migrate : Skip running migrations}
                              {--no-seed : Skip seeding roles/permissions}';

    protected $description = 'Install or re-initialise the SellUpNow admin panel on a fresh server.';

    public function handle(): int
    {
        $this->newLine();
        $this->line('╔══════════════════════════════════════╗');
        $this->line('║   SellUpNow Admin – Installation     ║');
        $this->line('╚══════════════════════════════════════╝');
        $this->newLine();

        // ── 1. Env sanity check ───────────────────────────────────────────────
        if (empty(config('app.key'))) {
            $this->warn('APP_KEY is not set. Generating one now...');
            Artisan::call('key:generate', ['--force' => true]);
            $this->info('APP_KEY generated. ✓');
        }

        // ── 2. Check own DB connection ────────────────────────────────────────
        $this->info('Checking own database connection...');
        try {
            DB::connection()->getPdo();
            $this->info('Own DB connection OK. ✓');
        } catch (\Exception $e) {
            $this->error('Could not connect to the SellUpNow admin database:');
            $this->error($e->getMessage());
            $this->line('Check DB_CONNECTION / DB_DATABASE in your .env file.');
            return self::FAILURE;
        }

        // ── 3. Check ListOcean bridge connection ──────────────────────────────
        $this->info('Checking ListOcean bridge connection...');
        try {
            DB::connection('listocean')->getPdo();
            $this->info('ListOcean bridge connection OK. ✓');
        } catch (\Exception $e) {
            $this->warn('ListOcean bridge connection failed:');
            $this->warn($e->getMessage());
            $this->warn('You can fix this after installation by updating LISTOCEAN_DB_* in .env.');
        }

        // ── 4. Migrations ─────────────────────────────────────────────────────
        if (!$this->option('no-migrate')) {
            $this->info('Running database migrations...');
            Artisan::call('migrate', ['--force' => true], $this->output);
            $this->info('Migrations complete. ✓');
        }

        // ── 5. Seed roles & permissions ───────────────────────────────────────
        if (!$this->option('no-seed')) {
            $this->info('Seeding roles and permissions...');
            try {
                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\PermissionSeeder',
                    '--force' => true,
                ], $this->output);

                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\RoleSeeder',
                    '--force' => true,
                ], $this->output);

                $this->info('Roles and permissions seeded. ✓');
            } catch (\Exception $e) {
                $this->warn('Could not seed roles/permissions: ' . $e->getMessage());
            }
        }

        // ── 6. Create super admin ─────────────────────────────────────────────
        $existingRoot = User::role(Roles::ROOT->value)->first();
        if ($existingRoot) {
            $this->warn('A root admin already exists: ' . $existingRoot->email);
            if (!$this->option('force') && !$this->confirm('Create another root admin?', false)) {
                $this->info('Skipping admin creation.');
            } else {
                $this->createAdmin();
            }
        } else {
            $this->info('No root admin found. Let\'s create one.');
            $this->createAdmin();
        }

        // ── 7. Clear all caches ───────────────────────────────────────────────
        $this->info('Clearing caches...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        $this->newLine();
        $this->line('╔═══════════════════════════════════════╗');
        $this->line('║   Installation complete!              ║');
        $this->line('╚═══════════════════════════════════════╝');
        $this->info('Visit ' . config('app.url') . '/admin to log in.');
        $this->newLine();

        return self::SUCCESS;
    }

    private function createAdmin(): void
    {
        $name     = $this->ask('Admin full name', 'Super Admin');
        $email    = $this->ask('Admin email');

        while (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address.');
            $email = $this->ask('Admin email');
        }

        $password = $this->secret('Admin password (min 8 chars)');
        while (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters.');
            $password = $this->secret('Admin password (min 8 chars)');
        }

        try {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name'              => $name,
                    'password'          => Hash::make($password),
                    'phone'             => '00000000000',
                    'is_active'         => true,
                    'email_verified_at' => now(),
                    'phone_verified_at' => now(),
                ]
            );

            // Ensure the root role exists before assigning
            Role::firstOrCreate(['name' => Roles::ROOT->value, 'guard_name' => 'web']);
            $user->assignRole(Roles::ROOT->value);

            $this->info("Admin created: {$user->email} ✓");
        } catch (\Exception $e) {
            $this->error('Failed to create admin: ' . $e->getMessage());
        }
    }
}
