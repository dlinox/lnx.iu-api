<?php

namespace App\Modules\Group\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'course' => $this->course,
            'code' => $this->code,
            'area' => $this->area,
            'module' => $this->module,
            'presentialPrice' => array_filter(explode(',', $this->presential_price)),
            'virtualPrice' => array_filter(explode(',', $this->virtual_price)),
            'modulePrice' => array_filter(explode(',', $this->module_price)),
            'countGroups' => $this->count_groups,
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}
