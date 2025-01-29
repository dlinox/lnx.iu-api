<?php

namespace App\Modules\Person\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}