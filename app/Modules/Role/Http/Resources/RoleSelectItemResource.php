<?php

namespace App\Modules\Role\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'value' => $this->id,
            'label' => $this->name,
        ];
        return parent::toArray($request);
    }
}
