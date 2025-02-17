<?php

namespace App\Modules\PreEnrollment\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class PreEnrollment extends Model
{
    use HasDataTable, HasEnabledState;

    protected $fillable = [
        'student_id',
        'group_id',
        'period_id',
    ];
}
