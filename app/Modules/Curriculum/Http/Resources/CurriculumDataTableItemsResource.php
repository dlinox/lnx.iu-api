<?php

namespace App\Modules\Curriculum\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CurriculumDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'gradingModel' => $this->grading_model,
            'isEnabled' => $this->is_enabled,
        ];
    }
}
