<?php

namespace App\Modules\Area\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AreaDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'isEnabled' => $this->is_enabled,
            'curriculumId' => $this->curriculum_id,
            'curriculum' => $this->curriculum,
        ];
        return parent::toArray($request);
    }
}
