<?php

namespace App\Modules\Group\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Str;

class GroupSaveRequest extends BaseRequest
{

    public function rules()
    {

        //add validation:
        // schedule: {days: [], startHour: "", endHour: ""}
        return [
            'groups.*' => 'required|array',
            'groups.*.id' => 'nullable|int',
            'groups.*.name' => 'required|max:50',
            'groups.*.min_students' => 'required|int',
            'groups.*.max_students' => 'required|int',
            'groups.*.modality' => 'required|in:PRESENCIAL,VIRTUAL',
            'groups.*.teacher_id' => 'nullable|exists:teachers,id',
            'groups.*.laboratory_id' => 'nullable|exists:laboratories,id',
            'groups.*.schedule' => 'required',
            'groups.*.schedule.days' => 'required|array',
            'groups.*.schedule.start_hour' => 'required',
            'groups.*.schedule.end_hour' => 'required',
            'course_id' => 'required|exists:courses,id',
            'period_id' => 'required|exists:periods,id',
        ];
    }

    public function messages()
    {
        return [
            'groups.*.name.required' => 'El nombre es requerido',
            'groups.*.name.max' => 'El nombre no debe ser mayor a 50 caracteres',
            'groups.*.modality.required' => 'La modalidad es requerida',
            'groups.*.modality.in' => 'La modalidad no es válida',
            'groups.*.min_students.required' => 'El número mínimo de estudiantes es requerido',
            'groups.*.min_students.int' => 'El número mínimo de estudiantes no es válido',
            'groups.*.max_students.required' => 'El número máximo de estudiantes es requerido',
            'groups.*.max_students.int' => 'El número máximo de estudiantes no es válido',
            'groups.*.teacher_id.exists' => 'El profesor no es válido',
            'groups.*.laboratory_id.exists' => 'El laboratorio no es válido',
            'groups.*.schedule.required' => 'El horario es requerido',
            'groups.*.schedule.days.required' => 'Los días del horario son requeridos',
            'groups.*.schedule.days.array' => 'Los días del horario son requeridos',
            'groups.*.schedule.start_hour.required' => 'La hora de inicio es requerida',
            'groups.*.schedule.end_hour.required' => 'La hora de fin es requerida',

            'course_id.required' => 'El curso es requerido',
            'course_id.exists' => 'El curso no es válido',
            'period_id.required' => 'El periodo es requerido',
            'period_id.exists' => 'El periodo no es válido',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'min_students' => $this->input('minStudents', $this->min_students ?? 0),
            'max_students' => $this->input('maxStudents', $this->max_students ?? 0),
            'period_id' => $this->input('periodId', $this->period_id),
            'course_id' => $this->input('courseId', $this->course_id),
            'groups' => $this->mapToSnakeCase($this->input('groups', $this->groups)),
        ]);
    }


    private function mapToSnakeCase($groups)
    {
        if (!is_array($groups)) {
            return [];
        }

        return array_map(function ($group) {
            if (is_array($group)) {
                //schedule: {days: [], startHour: "", endHour: ""}
                $group['schedule'] = collect($group['schedule'])
                    ->mapWithKeys(fn($value, $key) => [Str::snake($key) => $value])
                    ->toArray();
                $group = collect($group)
                    ->mapWithKeys(fn($value, $key) => [Str::snake($key) => $value])
                    ->toArray();
            }
            return $group;
        }, $groups);
    }
}
