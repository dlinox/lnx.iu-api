<?php

namespace App\Modules\Laboratory\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LaboratoryDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'virtualLink' => $this->virtual_link,
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}
