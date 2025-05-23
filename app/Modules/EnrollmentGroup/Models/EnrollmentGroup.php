<?php

namespace App\Modules\EnrollmentGroup\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class EnrollmentGroup extends Model
{
    use HasDataTable, HasEnabledState;

    protected $fillable = [
        'student_id',
        'group_id',
        'period_id',
        'created_by',
        'enrollment_modality',
        'special_enrollment',
        'with_enrollment',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
