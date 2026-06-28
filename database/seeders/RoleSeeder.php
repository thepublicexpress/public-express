<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Role;
class RoleSeeder extends Seeder {
    public function run(): void {
        $roles = [
            ['name'=>'Admin',           'slug'=>'admin',           'permissions'=>json_encode(['all'])],
            ['name'=>'State Editor',    'slug'=>'state_editor',    'permissions'=>json_encode(['news.manage','news.publish','users.view'])],
            ['name'=>'District Editor', 'slug'=>'district_editor', 'permissions'=>json_encode(['news.manage','news.publish'])],
            ['name'=>'Reporter',        'slug'=>'reporter',        'permissions'=>json_encode(['news.create','news.own'])],
        ];
        foreach ($roles as $role) { Role::firstOrCreate(['slug'=>$role['slug']], $role); }
    }
}
