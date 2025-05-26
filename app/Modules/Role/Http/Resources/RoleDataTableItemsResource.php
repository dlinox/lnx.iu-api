<?php

namespace App\Modules\Role\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class RoleDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'permissions' => DB::table('role_has_permissions')
                ->where('role_id', $this->id)
                ->pluck('permission_id'),
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}
