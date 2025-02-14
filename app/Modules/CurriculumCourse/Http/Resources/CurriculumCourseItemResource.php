<?php

namespace App\Modules\CurriculumCourse\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CurriculumCourseItemResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'order' => $this->order,
            'code' => $this->code,
            'hoursPractice' => $this->hours_practice,
            'hoursTheory' => $this->hours_theory,
            'credits' => $this->credits,
            'courseId' => $this->course_id,
            'moduleId' => $this->module_id,
            'areaId' => $this->area_id,
            'curriculumId' => $this->curriculum_id,
            'preRequisiteId' => $this->pre_requisite_id,
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}
