<?php

namespace App\Modules\AcademicRecord\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class AcademicRecordDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {

        $records = json_decode($this->records_json, true);

        $records = collect($records)->sortByDesc('createdAt')->map(function ($record) {
            $record['createdAt'] = Carbon::parse($record['createdAt'])->format('d/m/Y H:i:s');
            return $record;
        })->values()->all();

        return [
            'id' => $this->id,
            'group' => $this->group,
            'course' => $this->course,
            'teacher' => $this->teacher,
            'period' => $this->period,
            'records' =>  $records,
            'recordCodes' => $this->recordCodes,
            'lastCreatedAt' => $this->last_created_at ? Carbon::parse($this->last_created_at)->format('d/m/Y H:i:s') : null,
        ];
    }
}
