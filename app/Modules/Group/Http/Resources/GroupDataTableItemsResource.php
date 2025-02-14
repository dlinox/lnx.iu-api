<?php

namespace App\Modules\Group\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'course' => $this->course,
            'code' => $this->code,
            'area' => $this->area,
            'module' => $this->module,
            'countGroups' => $this->count_groups,
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}
