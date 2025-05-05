<?php

namespace App\Modules\EnrollmentGroup\Models;

use Illuminate\Database\Eloquent\Model;

class EnrollmentGroupAttendance extends Model
{
    protected $fillable = [
        'enrollment_group_id',
        'date',
        'time',
        'status',
        'observations',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
