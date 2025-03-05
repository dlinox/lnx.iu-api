<?php

namespace App\Modules\Period\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Constants\MonthConstants;

class PeriodSelectItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'value' => $this->id,
            'label' => $this->year . ' - ' . MonthConstants::label($this->month),
        ];
        return parent::toArray($request);
    }
}
