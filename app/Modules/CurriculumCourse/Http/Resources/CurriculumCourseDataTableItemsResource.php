<?php

namespace App\Modules\CurriculumCourse\Http\Resources;

use App\Modules\CurriculumCourse\Models\CurriculumCourse;
use Illuminate\Http\Resources\Json\JsonResource;

class CurriculumCourseDataTableItemsResource extends JsonResource
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
            'course' => $this->course,
            'area' => $this->area,
            'module' => $this->module,
            'curriculumId' => $this->curriculum_id,
            'preRequisite' => $this->pre_requisite_id ? CurriculumCourse::getPreRequisiteById($this->pre_requisite_id)->name : null,
            'isExtracurricular' => $this->is_extracurricular,
            'isEnabled' => $this->is_enabled,
        ];
    }
}
