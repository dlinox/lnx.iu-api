<?php

namespace App\Modules\Period\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PeriodDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'year' => $this->year,
            'month' => $this->month,
        ];
        return parent::toArray($request);
    }
}
