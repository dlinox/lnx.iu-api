<?php

namespace App\Modules\EnrollmentGroup\Models;

use Illuminate\Database\Eloquent\Model;

class EnrollmentGrade extends Model
{

    protected $fillable = [
        'final_grade',
        'capacity_average',
        'attitude_grade',
        'enrollment_group_id',
    ];
}
