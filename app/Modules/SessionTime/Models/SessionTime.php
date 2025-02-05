<?php

namespace App\Modules\SessionTime\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class SessionTime extends Model
{
    use HasDataTable, HasEnabledState;

    protected $fillable = [
        'start_time',
        'end_time',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    static $searchColumns = [
        'start_time',
        'end_time',
    ];
}
