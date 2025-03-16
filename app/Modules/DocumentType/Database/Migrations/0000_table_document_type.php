<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->boolean('is_enabled')->default(true);
        });
        $sql = file_get_contents(__DIR__ . '/../Data/recovered.sql');
        DB::unprepared($sql);

        $group = [
            'name' => 'DocumentType',
            'name_es' => 'Tipo de Documento',
        ];

        $actions = [
            'create' => 'Crear',
            'read' => 'Leer',
            'update' => 'Actualizar',
            'delete' => 'Eliminar'
        ];

        foreach ($actions as $action => $action_es) {
            $permissions[] = [
                'name' => strtolower("{$group['name']}.{$action}"),
                'name_es' => "{$group['name_es']} - {$action_es}",
                'group' => $group['name'],
                'account_level' => 'admin',
                'guard_name' => 'sanctum',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Permission::insert($permissions);
    }

    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
