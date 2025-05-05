<?php

namespace App\Modules\Module\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ModuleDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'level' => $this->level,
            'description' => $this->description,
            'curriculum' => $this->curriculum,
            'curriculumId' => $this->curriculum_id,
            'isExtracurricular' => $this->is_extracurricular,
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}
