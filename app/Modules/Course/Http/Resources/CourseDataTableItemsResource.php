<?php

namespace App\Modules\Course\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'hoursPractice' => $this->hours_practice,
            'hoursTheory' => $this->hours_theory,
            'credits' => $this->credits,
            'area' => $this->area,
            'module' => $this->module,
            'curriculum' => $this->curriculum,
            'preRequisite' => $this->pre_requisite,
            'description' => $this->description,
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}
