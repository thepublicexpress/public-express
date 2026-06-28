<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
class AdminUserSeeder extends Seeder {
    public function run(): void {
        User::firstOrCreate(
            ['email' => 'jy27986@gmail.com'],
            [
                'name'     => 'Admin',
                'role_id'  => Role::ADMIN,
                'password' => bcrypt('Bholu@2019'),
                'is_active'=> true,
                'email_verified_at' => now(),
            ]
        );
    }
}
