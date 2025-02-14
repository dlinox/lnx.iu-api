<?php

namespace App\Modules\Group\Http\Resources;

use App\Modules\Schedule\Http\Resources\ScheduleFormItemResource;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupFormItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'teacherId' => $this->teacher_id,
            'laboratoryId' => $this->laboratory_id,
            'modality' => $this->modality,
            'curriculumCourseId' => $this->curriculum_course_id,
            'schedules' =>  ScheduleFormItemResource::collection($this->schedules),
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}
