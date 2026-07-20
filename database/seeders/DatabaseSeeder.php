<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@navkwagroup.com');
        $adminPassword = env('ADMIN_PASSWORD');

        if ($adminPassword) {
            User::updateOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => env('ADMIN_NAME', 'Navkwa Administrator'),
                    'phone' => env('ADMIN_PHONE'),
                    'job_title' => 'Administrator',
                    'department' => 'Management',
                    'role' => 'Super Admin',
                    'account_status' => 'active',
                    'password' => Hash::make($adminPassword),
                ],
            );
        }
    }
}
