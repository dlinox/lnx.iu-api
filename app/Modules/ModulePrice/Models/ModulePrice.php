<?php

namespace App\Modules\ModulePrice\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class ModulePrice extends Model
{
    use HasDataTable, HasEnabledState;

    protected $fillable = [
        'curriculum_id',
        'module_id',
        'student_type_id',
        'price',
        'is_enabled',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_enabled' => 'boolean',
        'enrollmentPrice' => 'decimal:2',
    ];

    static $searchColumns = [
        'module_prices.id',
        'modules.name',
        'student_types.name',
    ];
}
