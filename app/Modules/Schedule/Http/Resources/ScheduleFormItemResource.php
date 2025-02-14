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
            'startHour' =>  Carbon::today()->setTimeFromTimeString($this->start_hour)->timestamp * 1000,
            'endHour' => Carbon::today()->setTimeFromTimeString($this->end_hour)->timestamp * 1000,
        ];
        return parent::toArray($request);
    }
}
