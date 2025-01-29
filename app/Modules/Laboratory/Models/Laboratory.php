<?php

namespace App\Modules\Laboratory\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    use HasDataTable, HasEnabledState;

    protected $fillable = [
        'name',
        'device_count',
        'device_detail',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'device_count' => 'integer',
    ];

    static $searchColumns = [
        'name',
    ];
}
