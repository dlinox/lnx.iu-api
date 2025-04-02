<?php

namespace App\Modules\EnrollmentDeadline\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use App\Traits\HasLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EnrollmentDeadline extends Model
{
    use HasDataTable, HasEnabledState, HasLogs;

    protected $fillable = [
        'start_date',
        'end_date',
        'type',
        'reference_id',
        'observations',
        'period_id',
        'virtual',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    static $searchColumns = [
        'enrollment_deadlines.start_date',
        'enrollment_deadlines.end_date',
        'enrollment_deadlines.type',
        'CONCAT( periods.year, "-",view_month_constants.label)',
    ];

    protected $casts = [
        'virtual' => 'boolean',
    ];

    protected $logAttributes = [
        'start_date',
        'end_date',
        'type',
        'observations',
        'period_id',
    ];

    protected $logName = 'Periodo de matrícula';

    public static function createRegular($data)
    {
        $data['type'] = 'REGULAR';
        return self::create($data);
    }

    public static function createExtension($data, $id)
    {
        $data['type'] = 'AMPLIACION';
        $data['reference_id'] = $id;
        $data['id'] = null;
        return self::create($data);
    }

    public static function activeEnrollmentPeriod()
    {
        $period = self::select(
            'enrollment_deadlines.period_id as periodId',
            'enrollment_deadlines.type',
            'enrollment_deadlines.start_date as startDate',
            'enrollment_deadlines.end_date as endDate',
            'enrollment_deadlines.virtual',
            DB::raw('CONCAT(periods.year, "-", view_month_constants.label) as period')
        )
            ->join('periods', 'enrollment_deadlines.period_id', '=', 'periods.id')
            ->join('view_month_constants', 'periods.month', '=', 'view_month_constants.value')
            ->where('enrollment_deadlines.start_date', '<=', now())  // Verifica que el período ya inició
            ->where('enrollment_deadlines.end_date', '>=', now())   // Verifica que el período no haya terminado
            ->first();

        return $period ? $period->toArray() : null;
    }
}
