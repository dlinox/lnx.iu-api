<?php

namespace App\Modules\EnrollmentGroup\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class EnrollmentGroup extends Model
{
    use HasDataTable, HasEnabledState;

    protected $fillable = [
        'day',
        'start_hour',
        'end_hour',
    ];
    public $timestamps = false;
}
