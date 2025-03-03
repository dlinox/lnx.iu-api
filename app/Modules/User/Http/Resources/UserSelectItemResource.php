<?php

namespace App\Modules\User\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSelectItemResource extends JsonResource
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
