<?php

namespace App\Modules\User\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use phpDocumentor\Reflection\Types\Boolean;

class UserDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->roles->first()->id,
            'roleName' => $this->roles->first()->name,
            'modelId' => $this->model_id,
            'isEnabled' => (bool)$this->is_enabled,
        ];
        return parent::toArray($request);
    }
}
