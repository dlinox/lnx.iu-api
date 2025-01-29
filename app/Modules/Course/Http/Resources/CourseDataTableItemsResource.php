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
            'description' => $this->description,
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}