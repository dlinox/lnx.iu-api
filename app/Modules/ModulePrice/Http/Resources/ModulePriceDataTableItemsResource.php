<?php

namespace App\Modules\ModulePrice\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Ramsey\Uuid\Type\Decimal;

class ModulePriceDataTableItemsResource extends JsonResource
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
            'price' =>  (float) $this->price,
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}
