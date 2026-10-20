<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = config('admin.seed_email');
        $password = config('admin.seed_password');

        if (! is_string($email) || ! is_string($password) || $email === '' || $password === '') {
            return;
        }

        Admin::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => config('admin.seed_name', 'مدير النظام'),
                'password' => Hash::make($password),
                'is_active' => true,
            ],
        );
    }
}
