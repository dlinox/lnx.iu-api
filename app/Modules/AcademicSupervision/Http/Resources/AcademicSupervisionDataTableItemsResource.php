<?php

namespace App\Modules\AcademicSupervision\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AcademicSupervisionDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'group' => $this->group,
            'groupId' => $this->group_id,
            'period' => $this->period,
            'course' => $this->course,
            'teacher' => $this->teacher,
            'type' => $this->type,
            'observations' => $this->observations,
        ];
        return parent::toArray($request);
    }
}
