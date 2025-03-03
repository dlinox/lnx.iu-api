<?php

namespace App\Modules\Role\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            // '1,2,3,4,5' = ['1', '2', '3', '4', '5']
            'permissions' => explode(',', $this->permissions),
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}
