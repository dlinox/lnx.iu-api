<?php

namespace App\Modules\Period\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Period extends Model
{
    use HasDataTable, HasEnabledState;

    protected $fillable = [
        'year',
        'month',
        'enrollment_enabled',
        'is_enabled',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'enrollment_enabled' => 'boolean',
        'is_enabled' => 'boolean',
    ];

    static $searchColumns = [
        'periods.year',
        'periods.month',
    ];

    //get current period
    public static function current()
    {
        $period = self::select(
            'periods.id as id', 
            DB::raw('CONCAT(year, "-", view_month_constants.label) as name'),
            'periods.enrollment_enabled as enrollmentEnabled',
        )->join('view_month_constants', 'periods.month', '=', 'view_month_constants.value')
            ->where('is_enabled', true)
            ->first();

        return $period ? $period : null;
    }
}
