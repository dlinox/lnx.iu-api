<?php

namespace App\Modules\Enrollment\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentEnrollmentItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'deviceCount' => $this->device_count,
            'deviceDetail' => $this->device_detail,
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}
