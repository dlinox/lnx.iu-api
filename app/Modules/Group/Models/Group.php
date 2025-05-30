<?php

namespace App\Modules\Group\Models;

use App\Modules\Schedule\Models\Schedule;
use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasDataTable;

    protected $fillable = [
        'name',
        'period_id',
        'teacher_id',
        'laboratory_id',
        'course_id',
        'status',
        'modality',
        'observation',
        'min_students',
        'max_students',
    ];

    protected $casts = [
        // 'is_enabled' => 'boolean',
    ];

    //has many schedules
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
