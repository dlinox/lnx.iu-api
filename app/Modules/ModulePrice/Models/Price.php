<?php

namespace App\Modules\Price\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasDataTable, HasEnabledState;
    protected $fillable = [
        'curriculum_id',
        'module_id',
        'student_type_id',
        'enrollment_price',
        'presential_price',
        'virtual_price',
        'is_enabled',
    ];

    protected $casts = [
        'enrollment_price' => 'decimal:2',
        'presential_price' => 'decimal:2',
        'virtual_price' => 'decimal:2',
        'is_enabled' => 'boolean',
        'enrollmentPrice' => 'decimal:2',
    ];

    static $searchColumns = [
        'prices.id',
        'modules.name',
        'student_types.name',
    ];
}
