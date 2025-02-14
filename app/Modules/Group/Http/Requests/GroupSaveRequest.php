<?php

namespace App\Modules\Group\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Str;

class GroupSaveRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'groups.*' => 'required|array',
            'groups.*.id' => 'nullable|int',
            'groups.*.name' => 'required|max:50',
            'groups.*.modality' => 'required|in:PRESENCIAL,VIRTUAL,MIXTO',
            'groups.*.is_enabled' => 'required|boolean',
            'groups.*.teacher_id' => 'nullable|exists:teachers,id',
            'groups.*.laboratory_id' => 'nullable|exists:laboratories,id',
            'groups.*.schedules' => 'array',
            'groups.*.schedules.*.day' => 'required|in:LUN,MAR,MIE,JUE,VIE,SAB,DOM',
            'groups.*.schedules.*.end_hour' => 'required',
            'groups.*.schedules.*.start_hour' => 'required',
            'curriculum_course_id' => 'required|exists:curriculum_courses,id',
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
            'groups.*.is_enabled.required' => 'El estado es requerido',
            'groups.*.is_enabled.boolean' => 'El estado no es válido',
            'groups.*.teacher_id.exists' => 'El profesor no es válido',
            'groups.*.laboratory_id.exists' => 'El laboratorio no es válido',
            'groups.*.schedules.array' => 'Los horarios no son válidos',
            'groups.*.schedules.*.day.required' => 'El día es requerido',
            'groups.*.schedules.*.day.in' => 'El día no es válido',
            'groups.*.schedules.*.end_hour.required' => 'La hora de fin es requerida',
            'groups.*.schedules.*.start_hour.required' => 'La hora de inicio es requerida',
            'curriculum_course_id.required' => 'El curso es requerido',
            'curriculum_course_id.exists' => 'El curso no es válido',
            'period_id.required' => 'El periodo es requerido',
            'period_id.exists' => 'El periodo no es válido',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'period_id' => $this->input('periodId', $this->period_id),
            'curriculum_course_id' => $this->input('curriculumCourseId', $this->curriculum_course_id),
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
                $group = collect($group)
                    ->mapWithKeys(fn($value, $key) => [Str::snake($key) => $value])
                    ->toArray();
                if (isset($group['schedules']) && is_array($group['schedules'])) {
                    $group['schedules'] = $this->mapToSnakeCase($group['schedules']);
                }
            }
            return $group;
        }, $groups);
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = collect($validator->errors())->mapWithKeys(function ($messages, $field) {
            return [
                Str::camel($field) => $messages[0]
            ];
        });

        throw new HttpResponseException(
            response()->json(
                [
                    'errors' => $errors,
                    'message' => 'Error al guardar los registros, verifique los datos ingresados'
                ],
                422
            )
        );
    }
}
