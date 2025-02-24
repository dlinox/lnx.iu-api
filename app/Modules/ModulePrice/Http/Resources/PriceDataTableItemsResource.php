<?php

namespace App\Modules\Price\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Ramsey\Uuid\Type\Decimal;

class PriceDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'curriculumId' => $this->curriculum_id,
            'moduleId' => $this->module_id,
            'module' => $this->module,
            'studentTypeId' => $this->student_type_id,
            'studentType' => $this->student_type,
            'enrollmentPrice' => (float) $this->enrollment_price,
            'presentialPrice' =>  (float) $this->presential_price,
            'virtualPrice' => (float) $this->virtual_price,
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}
