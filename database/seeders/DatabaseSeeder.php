<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Modules\User\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
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
            'account_level' => 'admin',
            'is_enabled' => true,
        ]);


        $modules = [
            'Area' => 'Área',
            'Authentication' => 'Autenticación',
            'Course' => 'Curso',
            'CoursePrice' => 'Precio del Curso',
            'Curriculum' => 'Currículum',
            'dashboard' => 'Tablero',
            'DocumentType' => 'Tipo de Documento',
            'Enrollment' => 'Matrícula',
            'Group' => 'Grupo',
            'Laboratory' => 'Laboratorio',
            'Module' => 'Módulo',
            'ModulePrice' => 'Precio del Módulo',
            'PaymentType' => 'Tipo de Pago',
            'Period' => 'Período',
            'Person' => 'Persona',
            'Price' => 'Precio',
            'Student' => 'Estudiante',
            'StudentType' => 'Tipo de Estudiante'
        ];

        $actions = [
            'create' => 'Crear',
            'read' => 'Leer',
            'update' => 'Actualizar',
            'delete' => 'Eliminar'
        ];

        $permissions = [];

        foreach ($modules as $module => $module_es) {
            foreach ($actions as $action => $action_es) {
                $permissions[] = [
                    'name' => strtolower("{$module}.{$action}"),
                    'display_name' => "$action_es $module_es",
                    'group' => $module,
                    'account_level' => 'admin',
                    'guard_name' => 'sanctum',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        Permission::insert($permissions);

        $role = Role::create(['name' => 'admin']);
        Role::create(['name' => 'estudiante']);
        Role::create(['name' => 'docente']);
        $user = User::find(1);
        $user->assignRole($role);
    }
}
