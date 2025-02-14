<?php

namespace App\Modules\Period\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
//MONTH CONSTANTS
use App\Constants\MonthConstants;

class PeriodDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'value' => $this->id,
            'label' => $this->year . ' - ' . MonthConstants::label($this->month) . ' - ' . ($this->is_enabled ? 'ACTIVO' : 'INACTIVO'),
        ];
        return parent::toArray($request);
    }
}
