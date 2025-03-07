<?php

namespace App\Modules\Enrollment\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasDataTable, HasEnabledState;

    protected $fillable = [ 
        // 'curriculum_id',
        'student_id',
        'module_id',
        // 'payment_id',
    ];
}
