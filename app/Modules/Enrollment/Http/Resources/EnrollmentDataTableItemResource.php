<?php

namespace App\Modules\Enrollment\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentDataTableItemResource extends JsonResource
{
    public function toArray($request)
    {
        /*
            'enrollment_groups.id as id',
                'modules.name as module',
                'enrollment_groups.status as enrollmentStatus',
                'groups.id as groupId',
                'groups.name as group',
                'groups.modality as modality',
                'laboratories.name as laboratory',
                DB::raw('CONCAT_WS(" ", people.name, people.last_name_father, people.last_name_mother) as student'),
                DB::raw('CONCAT_WS("-", periods.year, view_month_constants.label) as period'),
                DB::raw('CONCAT_WS("- ", courses.code, courses.name) as course'),
        */
        return [

            'id' => $this->id,
            'module' => $this->module,
            'enrollmentStatus' => $this->enrollmentStatus,
            'groupId' => $this->groupId,
            'group' => $this->group,
            'modality' => $this->modality,
            'laboratory' => $this->laboratory,
            'student' => $this->student,
            'period' => $this->period,
            'course' => $this->course,
        ];
    }
}
