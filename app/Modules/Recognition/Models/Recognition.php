<?php

namespace App\Modules\Recognition\Models;

use App\Traits\HasDataTable;
use Illuminate\Database\Eloquent\Model;

class Recognition extends Model
{

    use HasDataTable;

    protected $fillable = [
        'module_id',
        'enrollment_group_id',
        'course_recognition_id',
        'student_id',
        'observation',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
