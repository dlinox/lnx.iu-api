<?php

namespace App\Modules\CurriculumModule\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CurriculumModuleDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order' => $this->order,
            'isEnabled' => $this->is_enabled,
            'isExtracurricular' => $this->is_extracurricular,
            'area' => $this->area,
            'module' => $this->module,
            'curriculum' => $this->curriculum,
        ];
    }
}
