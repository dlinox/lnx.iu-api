<?php

namespace App\Modules\GradeDeadline\Http\Resources;

use App\Modules\GradeDeadline\Models\GradeDeadline;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeDeadlineDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'type' => $this->type,
            'observations' => $this->observations,
            'periodId' => $this->period_id,
            'period' => $this->period,
            'lastDate' => GradeDeadline::where(function ($query) {
                $query
                    ->where('id', $this->id)
                    ->orWhere('reference_id', $this->id);
            })
                ->orderBy('end_date', 'desc')
                ->first()
                ->end_date,
        ];
        return parent::toArray($request);
    }
}
