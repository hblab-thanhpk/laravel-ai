<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUsersSeeder extends Seeder
{
    /**
     * Seed admin-access users with deterministic credentials.
     */
    public function run(): void
    {
        $adminRoleId = Role::query()
            ->where('name', 'admin')
            ->value('id');

        $users = [
            [
                'name' => 'Admin Root',
                'email' => 'admin@example.com',
            ],
            [
                'name' => 'Admin Ops',
                'email' => 'ops.admin@example.com',
            ],
            [
                'name' => 'Admin Support',
                'email' => 'support.admin@example.com',
            ],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_admin' => true,
                    'role_id' => $adminRoleId,
                ],
            );
        }
    }
}
