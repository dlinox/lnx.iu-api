<?php

namespace App\Modules\User\Models;

use App\Traits\HasDataTable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use  Notifiable,  HasRoles, HasApiTokens, HasDataTable, HasPermissions;

    protected $fillable = [
        'name',
        'username',
        'email',
        'model_id',
        'model_type',
        'email_verified_at',
        'password',
        'is_enabled',
    ];

    protected $hidden = [
        'level_acount',
        'password',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    static $searchColumns = [
        'users.name',
        'users.username',
        'users.email',
    ];

    public static function createAccountStudent($student)
    {
        $password = rand(10000000, 99999999);
        $item =  self::create([
            'name' => $student['name'] . ' ' . $student['last_name_father'] . ' ' . $student['last_name_mother'],
            'username' => $student['document_number'],
            'email' => $student['email'],
            'password' => $password,
            'model_id' => $student['id'],
            'is_enabled' => true,
            'model_type' => 'student',
        ]);

        $item->syncRoles(['estudiante']);

        return [
            'password' => $password,
            'username' => $student['document_number'],
        ];
    }

    public static function createAccountTeacher($teacher)
    {
        $password = rand(10000000, 99999999);
        $item =  self::create([
            'name' => $teacher['name'] . ' ' . $teacher['last_name_father'] . ' ' . $teacher['last_name_mother'],
            'username' => $teacher['document_number'],
            'email' => $teacher['email'],
            'password' => $password,
            'model_id' => $teacher['id'],
            'is_enabled' => true,
            'model_type' => 'teacher',
        ]);

        $item->syncRoles(['docente']);

        return [
            'password' => $password,
            'username' => $teacher['document_number'],
        ];
    }
}
