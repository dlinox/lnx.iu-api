<?php

namespace App\Modules\SessionTime\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SessionTimeDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'startTime' => $this->start_time,
            'endTime' => $this->end_time,
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}