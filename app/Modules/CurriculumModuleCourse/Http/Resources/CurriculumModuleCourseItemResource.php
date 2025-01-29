<?php

namespace App\Modules\CurriculumModuleCourse\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CurriculumModuleCourseItemResource extends JsonResource
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
            'curriculumModuleId' => $this->curriculum_module_id,
            'preRequisiteId' => $this->pre_requisite_id,
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}
