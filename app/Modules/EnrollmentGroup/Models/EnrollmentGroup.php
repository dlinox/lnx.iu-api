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
        // 'payment_id',
    ];
}
