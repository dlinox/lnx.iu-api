<?php

namespace App\Modules\Group\Http\Resources;

use App\Modules\Schedule\Http\Resources\ScheduleFormItemResource;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupFormItemResource extends JsonResource
{
    public function toArray($request)
    {
        $days = $this->schedules->map(function ($schedule) {
            return $schedule->day;
        });
        $days = $days->unique();
        $days = $days->values();

        $schedule = [
            'days' => $days,
            'startHour' => $this->schedules[0]->start_hour ?? '',
            'endHour' => $this->schedules[0]->end_hour ?? '',
        ];

        return [
            'id' => $this->id,
            'name' => $this->name,
            'teacherId' => $this->teacher_id,
            'laboratoryId' => $this->laboratory_id,
            'modality' => $this->modality,
            'minStudents' => $this->min_students,
            'maxStudents' => $this->max_students,
            'courseId' => $this->course_id,
            'schedule' =>  $schedule,
            'status' => $this->status,
        ];
        return parent::toArray($request);
    }
}
