<?php

namespace App\Modules\AcademicSupervision\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use App\Traits\HasLogs;
use Illuminate\Database\Eloquent\Model;

class AcademicSupervision extends Model
{
    use HasDataTable, HasEnabledState, HasLogs;

    protected $fillable = [
        'group_id',
        'teacher_id',
        'user_id',
        'type',
        'observations',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    static $searchColumns = [
        'CONCAT(teachers.name, " ", teachers.last_name_father, " ", teachers.last_name_mother)'
    ];

    protected $casts = [
        'virtual' => 'boolean',
    ];

    protected $logAttributes = [
        'group_id',
        'user_id',
        'teacher_id',
        'type',
        'observations',
    ];

    protected $logName = 'Supervisión Académica';
}
