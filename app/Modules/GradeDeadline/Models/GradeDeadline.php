<?php

namespace App\Modules\GradeDeadline\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use App\Traits\HasLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GradeDeadline extends Model
{
    use HasDataTable, HasEnabledState, HasLogs;

    protected $fillable = [
        'start_date',
        'end_date',
        'type',
        'reference_id',
        'observations',
        'period_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    static $searchColumns = [
        'grade_deadlines.start_date',
        'grade_deadlines.end_date',
        'grade_deadlines.type',
        'CONCAT( periods.year, "-",months.name)',
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

    public static function activeGradePeriod()
    {
        $period = self::select(
            'grade_deadlines.period_id as periodId',
            'grade_deadlines.type',
            'grade_deadlines.start_date as startDate',
            'grade_deadlines.end_date as endDate',
            DB::raw('CONCAT(periods.year, "-", upper(months.name)) as period')
        )
            ->join('periods', 'grade_deadlines.period_id', '=', 'periods.id')
            ->join('months', 'periods.month', '=', 'months.id')
            ->where('grade_deadlines.start_date', '<=', now())  // Verifica que el período ya inició
            ->where('grade_deadlines.end_date', '>=', now())   // Verifica que el período no haya terminado
            ->first();

        return $period ? $period->toArray() : null;
    }
}
