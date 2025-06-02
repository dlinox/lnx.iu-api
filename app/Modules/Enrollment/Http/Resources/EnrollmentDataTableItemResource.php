<?php

namespace App\Modules\Enrollment\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use phpDocumentor\Reflection\Types\Boolean;

class EnrollmentDataTableItemResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'module' => $this->module,
            'enrollmentStatus' => $this->enrollmentStatus,
            'enrollmentModality' => $this->enrollmentModality,
            'groupId' => $this->groupId,
            'group' => $this->group,
            'modality' => $this->modality,
            'laboratory' => $this->laboratory,
            'studentId' => $this->studentId,
            'student' => $this->student,
            'period' => $this->period,
            'courseId' => $this->courseId,
            'course' => $this->course,
            'isSpecial' => (Boolean)$this->isSpecial,
        ];
    }
}
