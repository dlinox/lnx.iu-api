<?php

namespace App\Modules\AcademicRecord\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use App\Traits\HasLogs;
use Illuminate\Database\Eloquent\Model;

class AcademicRecord extends Model
{
    use HasDataTable, HasEnabledState, HasLogs;

    protected $fillable = [
        'group_id',
        'created_by',
        'grade_deadline_id',
        'payload',
        'code',
        'is_enabled',
        'observations',
    ];

    protected $hidden = [];

    static $searchColumns = [
        'CONCAT(teachers.name, " ", teachers.last_name_father, " ", teachers.last_name_mother)',
        'CONCAT(periods.year, " ", months.name)',
        'groups.name',
        'courses.name'
    ];

    protected $casts = [
        'payload' => 'array',
        'group_id' => 'integer',
        'created_by' => 'integer',
        'is_enabled' => 'boolean',
        'grade_deadline_id' => 'integer',
    ];

    protected $logAttributes = [
        'group_id',
        'is_enabled',
        'code',
    ];

    protected $logName = 'Registro Académico';
}
