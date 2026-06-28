<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles insert karein
        DB::table('roles')->insert([
            ['name' => 'super_admin', 'slug' => 'super-admin'],
            ['name' => 'state_reporter', 'slug' => 'state-reporter'],
            ['name' => 'district_reporter', 'slug' => 'district-reporter'],
            ['name' => 'tehsil_reporter', 'slug' => 'tehsil-reporter'],
            ['name' => 'subscriber', 'slug' => 'subscriber'],
        ]);

        // 2. Admin User banayein
        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'admin@thepublicexpress.com',
            'password' => Hash::make('password123'), // Login password: password123
            'role_id' => 1, // Super Admin ka role_id
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Revenue Settings
        DB::table('revenue_settings')->insert([
            'views_per_point' => 100,
            'point_value' => 0.10,
            'min_withdrawal' => 100,
            'max_withdrawal' => 10000,
            'points_per_news' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}