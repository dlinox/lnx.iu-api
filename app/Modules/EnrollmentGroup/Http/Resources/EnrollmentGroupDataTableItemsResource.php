<?php

namespace App\Modules\EnrollmentGroup\Http\Resources;

use App\Modules\EnrollmentGroup\Models\EnrollmentGroup;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentGroupDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {

        $studentEnrolled = EnrollmentGroup::where('group_id', $this->id)->where('status', 'MATRICULADO')->count();
        $studentReserved = EnrollmentGroup::where('group_id', $this->id)->where('status', 'RESERVADO')->count();
        $percentageOpening = $this->minStudents > 0 ? $studentEnrolled / $this->minStudents * 100 : 0;

        //percentageOpening  max 2 decimal
        $percentageOpening = number_format($percentageOpening, 1);

        return [
            'id' => $this->id,
            'group' => $this->group,
            'modality' => $this->modality,
            'minStudents' => $this->minStudents,
            'maxStudents' => $this->maxStudents,
            'status' => $this->status,
            'module' => $this->module,
            'area' => $this->area,
            'course' => $this->course,
            'studentEnrolled' => $studentEnrolled,
            'studentReserved' => $studentReserved,
            'percentageOpening' => $percentageOpening,

        ];
        return parent::toArray($request);
    }
}
