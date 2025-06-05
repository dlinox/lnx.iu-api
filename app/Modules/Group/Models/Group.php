<?php

namespace App\Modules\Group\Models;

use App\Modules\Schedule\Models\Schedule;
use App\Traits\HasDataTable;
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

    public static function getDetails($groupId)
    {
        $group = Group::select(
            'groups.name as groupName',
            'groups.modality as modality',
            'courses.name as courseName',
        )
            ->join('courses', 'courses.id', '=', 'groups.course_id')
            ->where('groups.id', $groupId)
            ->first();

        $group['schedule'] = Schedule::byGroup($groupId);

        return $group;
    }
}
