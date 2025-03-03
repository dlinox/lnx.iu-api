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

    public static function current()
    {
        $period = self::select(
            'periods.id as id',
            DB::raw('CONCAT(year, "-", view_month_constants.label) as name'),
        )->join('view_month_constants', 'periods.month', '=', 'view_month_constants.value')
            ->where('is_enabled', true)
            ->first();

        return $period ? $period : null;
    }

    public static function enrollmentPeriod()
    {
        $period = self::select(
            'periods.id as id',
            DB::raw('CONCAT(year, "-", view_month_constants.label) as name'),
        )->join('view_month_constants', 'periods.month', '=', 'view_month_constants.value')
            ->where('enrollment_enabled', true)
            ->first();

        return $period ? $period : null;
    }

    //activar periodo
    public function enableCurrent()
    {
        //desactivar todos los periodos
        self::where('is_enabled', true)->update(['is_enabled' => false]);
        $this->is_enabled = true;
        $this->save();
    }
    //activar periodo de inscripcion
    public function enableEnrollment()
    {
        //desactivar todos los periodos de inscripcion
        self::where('enrollment_enabled', true)->update(['enrollment_enabled' => false]);
        $this->enrollment_enabled = true;
        $this->save();
    }
}
