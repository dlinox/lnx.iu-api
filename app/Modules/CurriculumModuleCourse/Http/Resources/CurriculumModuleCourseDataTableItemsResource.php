<?php

namespace App\Modules\CurriculumModuleCourse\Http\Resources;

use App\Modules\CurriculumModuleCourse\Models\CurriculumModuleCourse;
use Illuminate\Http\Resources\Json\JsonResource;

class CurriculumModuleCourseDataTableItemsResource extends JsonResource
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
            'isEnabled' => $this->is_enabled,
            'preRequisite' => $this->pre_requisite_id ? CurriculumModuleCourse::getPreRequisiteById($this->pre_requisite_id)->name : null,
        ];
    }
}
