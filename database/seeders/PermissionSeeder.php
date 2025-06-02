<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = config('constants.permissions');
        if (empty($permissions)) {
            return;
        }
        
        //elimnar role_has_permissions
        DB::table('role_has_permissions')->truncate();
        //eliminar todas las permisos existentes
        Permission::query()->delete();
        //crear permisos
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
