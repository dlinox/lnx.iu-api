<?php

namespace App\Modules\Laboratory\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use App\Traits\HasLogs;
use Illuminate\Database\Eloquent\Model;

class Laboratory extends Model
{
    use HasDataTable, HasEnabledState, HasLogs;

    protected $fillable = [
        'name',
        'type',
        'virtual_link',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    static $searchColumns = [
        'name',
    ];

    protected $logAttributes = [
        'name',
        'type',
        'virtual_link',
        'is_enabled',
    ];

    protected $logName = 'Laboratorio';
}
