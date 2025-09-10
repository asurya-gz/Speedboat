<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DefaultUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Default password for all demo accounts
        $defaultPassword = Hash::make('speedboat123');

        // Create Admin user
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@speedboat.com',
            'password' => $defaultPassword,
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Kasir user
        User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir@speedboat.com',
            'password' => $defaultPassword,
            'role' => User::ROLE_KASIR,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Boarding user
        User::create([
            'name' => 'Boarding Officer',
            'email' => 'boarding@speedboat.com',
            'password' => $defaultPassword,
            'role' => User::ROLE_BOARDING,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create additional kasir user
        User::create([
            'name' => 'Kasir 2',
            'email' => 'kasir2@speedboat.com',
            'password' => $defaultPassword,
            'role' => User::ROLE_KASIR,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create additional boarding user
        User::create([
            'name' => 'Boarding Officer 2',
            'email' => 'boarding2@speedboat.com',
            'password' => $defaultPassword,
            'role' => User::ROLE_BOARDING,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
