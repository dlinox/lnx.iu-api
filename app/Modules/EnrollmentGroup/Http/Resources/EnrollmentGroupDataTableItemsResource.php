<?php

namespace App\Modules\EnrollmentGroup\Http\Resources;

use App\Modules\EnrollmentGroup\Models\EnrollmentGroup;
use App\Modules\Schedule\Models\Schedule;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentGroupDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {

        $studentEnrolled = EnrollmentGroup::where('group_id', $this->id)->where('status', 'MATRICULADO')->count();
        $studentReserved = EnrollmentGroup::where('group_id', $this->id)->where('status', 'RESERVADO')->count();
        $percentageOpening = $this->minStudents > 0 ? $studentEnrolled / $this->minStudents * 100 : 0;
        $percentageOpening = number_format($percentageOpening, 1);

        $schedules = Schedule::byGroup($this->id);

        if ($schedules) {
            $schedule = $schedules->days . ' - ' . $schedules->startHour . ' a ' . $schedules->endHour;
        }
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
            'teacherId' => $this->teacher_id,
            'teacher' => $this->teacher,
            'laboratoryId' => $this->laboratory_id,
            'laboratory' => $this->laboratory,
            'schedules' => $schedule,
            'studentEnrolled' => $studentEnrolled,
            'studentReserved' => $studentReserved,
            'percentageOpening' => $percentageOpening,

        ];
        return parent::toArray($request);
    }
}
