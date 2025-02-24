<?php

namespace App\Modules\CoursePrice\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class CoursePrice extends Model
{
    use HasDataTable, HasEnabledState;
    protected $fillable = [
        'curriculum_id',
        'course_id',
        'student_type_id',
        'presential_price',
        'virtual_price',
        'is_enabled',
    ];

    protected $casts = [
        'presential_price' => 'decimal:2',
        'virtual_price' => 'decimal:2',
        'is_enabled' => 'boolean',
        'enrollmentPrice' => 'decimal:2',
    ];

    static $searchColumns = [
        'course_prices.id',
        'courses.name',
        'student_types.name',
    ];
}
