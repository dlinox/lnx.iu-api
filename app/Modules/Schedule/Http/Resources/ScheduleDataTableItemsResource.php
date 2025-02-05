<?php

namespace App\Modules\Schedule\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'days' => $this->days,
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}