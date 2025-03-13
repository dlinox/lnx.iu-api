<?php

namespace App\Modules\Schedule\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleFormItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'day' => $this->day,
            'startHour' =>  $this->start_hour,
            'endHour' => $this->end_hour,
        ];
        return parent::toArray($request);
    }
}
