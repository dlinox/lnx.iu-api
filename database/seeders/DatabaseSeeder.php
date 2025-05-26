<?php

namespace Database\Seeders;

use App\Modules\User\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'username' => 'admin',
            'password' => 'admin',
            'email_verified_at' => now(),
            'model_type' => 'admin',
            'is_enabled' => true,
        ]);

        $role = Role::create(['name' => 'admin', 'guard_name' => 'sanctum', 'model_type' => 'admin']);
        Role::create(['name' => 'estudiante', 'model_type' => 'student']);
        Role::create(['name' => 'docente', 'model_type' => 'teacher']);
        $admin->assignRole($role);

        $permissions = config('constants.permissions');
        if (empty($permissions)) {
            return;
        }
        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission['name'],
                'display_name' => $permission['display_name'],
                'group' => $permission['group'],
                'model_type' => $permission['model_type'],
                'guard_name' => $permission['guard_name'],
            ]);
        }
    }
}
