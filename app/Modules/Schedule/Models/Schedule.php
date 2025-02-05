<?php

namespace App\Modules\Schedule\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasDataTable, HasEnabledState;

    protected $fillable = [
        'days',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    static $searchColumns = [
        'days',
    ];
}
