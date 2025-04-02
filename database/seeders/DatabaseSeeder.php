<?php

namespace Database\Seeders;

use App\Modules\User\Models\User;
use Illuminate\Database\Seeder;
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

        $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'model_type' => 'admin']);
        Role::create(['name' => 'estudiante', 'model_type' => 'student']);
        Role::create(['name' => 'docente', 'model_type' => 'teacher']);
        $admin->assignRole($role);
    }
}
