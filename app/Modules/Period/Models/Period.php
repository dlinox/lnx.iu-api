<?php

namespace App\Modules\Period\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasDataTable, HasEnabledState;

    protected $fillable = [
        'year',
        'month',
        'is_enabled',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'is_enabled' => 'boolean',
    ];

    static $searchColumns = [
        'year',
        'month',
    ];
}
