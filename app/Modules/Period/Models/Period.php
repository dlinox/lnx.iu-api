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
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['year', 'month'])
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
}
