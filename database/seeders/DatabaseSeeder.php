<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Modules\User\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'username' => 'admin',
            'password' => 'admin',
            'is_enabled' => true,
        ]);
        //crear rol
        $role = Role::create(['name' => 'admin']);
        //asignar rol al usuario
        $user = User::find(1);
        $user->assignRole($role);
    }
}
