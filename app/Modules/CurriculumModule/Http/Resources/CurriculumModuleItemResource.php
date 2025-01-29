<?php

namespace App\Modules\CurriculumModule\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CurriculumModuleItemResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'order' => $this->order,
            'areaId' => $this->area_id,
            'moduleId' => $this->module_id,
            'isExtracurricular' => $this->is_extracurricular,
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}
