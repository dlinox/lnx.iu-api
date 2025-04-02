<?php

namespace App\Modules\PreRegister\Models;

use Illuminate\Database\Eloquent\Model;

class PreRegister extends Model
{
    protected $fillable = [
        'token',
        'student_type',
        'student_code',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
