<?php

namespace App\Modules\EnrollmentDeadline\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use App\Traits\HasLogs;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    static $searchColumns = [
        'enrollment_periods.start_date',
        'enrollment_periods.end_date',
        'enrollment_periods.type',
        'CONCAT( periods.year, "-",view_month_constants.label)',
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
}
