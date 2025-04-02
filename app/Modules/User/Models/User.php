<?php

namespace App\Modules\User\Models;

use App\Traits\HasDataTable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use  Notifiable,  HasRoles, HasApiTokens, HasDataTable;

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

    public static function createAccountStudent($person, $studentId)
    {
        $password = rand(10000000, 99999999);
        $item =  self::create([
            'name' => $person['name'] . ' ' . $person['last_name_father'] . ' ' . $person['last_name_mother'],
            'username' => $person['document_number'],
            'email' => $person['email'],
            'password' => $password,
            'model_id' => $studentId,
            'is_enabled' => true,
            'model_type' => 'student',
        ]);

        $item->syncRoles(['estudiante']);

        return [
            'password' => $password,
            'username' => $person['document_number'],
        ];
    }
}
