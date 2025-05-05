<?php

namespace App\Modules\PaymentType\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentTypeDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'commission' => (float)$this->commission,
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}
