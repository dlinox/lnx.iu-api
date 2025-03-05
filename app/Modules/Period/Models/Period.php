<?php

namespace App\Modules\Period\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Period extends Model
{
    use HasDataTable, HasEnabledState, LogsActivity;

    protected $fillable = [
        'year',
        'month',
        'status',
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
        'view_month_constants.label',
        'periods.status',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'year', 'month'])
            ->logOnlyDirty()
            ->useLogName('periodo');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $eventNameSpanish = [
            'created' => 'creado',
            'updated' => 'actualizado',
            'deleted' => 'eliminado',
        ];
        $ip = request()->ip();
        return "{$ip}: El período ha sido {$eventNameSpanish[$eventName]}";
    }

    public static function current()
    {
        $period = self::select(
            'periods.id as id',
            DB::raw('CONCAT(year, "-", view_month_constants.label) as name'),
        )->join('view_month_constants', 'periods.month', '=', 'view_month_constants.value')
            ->where('status', 'EN CURSO')
            ->first();

        return $period ? $period : null;
    }

    public static function enrollmentPeriod()
    {
        $period = self::select(
            'periods.id as id',
            DB::raw('CONCAT(year, "-", view_month_constants.label) as name'),
        )->join('view_month_constants', 'periods.month', '=', 'view_month_constants.value')
            ->where('status', 'MATRICULA')
            ->first();

        return $period ? $period : null;
    }
}
