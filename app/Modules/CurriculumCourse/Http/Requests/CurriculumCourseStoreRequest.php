<?php

namespace App\Modules\CurriculumCourse\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class CurriculumCourseStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $moduleId = $this->module_id;
        $curriculumId = $this->curriculum_id;

        return [
            'order' => 'required|integer|unique:curriculum_courses,order,NULL,id,curriculum_id,' . $curriculumId . ',module_id,' . $moduleId,
            'code' => 'nullable|string|max:50',
            'hours_practice' => 'required|integer',
            'hours_theory' => 'required|integer',
            'credits' => 'required|integer',
            'course_id' => 'required|unique:curriculum_courses,course_id,NULL,id,curriculum_id,' . $curriculumId . ',module_id,' . $moduleId,
            'module_id' => 'nullable',
            'area_id' => 'required',
            'curriculum_id' => 'required',
            'is_extracurricular' => 'boolean',
            'pre_requisite_id' => 'nullable|integer',
            'is_enabled' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'order.required' => 'Obligatorio',
            'order.integer' => 'Debe ser un número entero',
            'order.unique' => 'Ya existe un registro con este orden',
            'hours_practice.required' => 'Obligatorio',
            'hours_practice.integer' => 'Debe ser un número entero',
            'hours_theory.required' => 'Obligatorio',
            'hours_theory.integer' => 'Debe ser un número entero',
            'credits.required' => 'Obligatorio',
            'credits.integer' => 'Debe ser un número entero',
            'course_id.required' => 'Obligatorio',
            'course_id.unique' => 'El curso ya existe en el modulo seleccionado',
            'area_id.required' => 'Obligatorio',
            'curriculum_id.required' => 'Obligatorio',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'module_id' => $this->input('moduleId', $this->module_id),
            'curriculum_id' => $this->input('curriculumId', $this->curriculum_id),
            'area_id' => $this->input('areaId', $this->area_id),
            'course_id' => $this->input('courseId', $this->course_id),
            'hours_practice' => $this->input('hoursPractice', $this->hours_practice),
            'hours_theory' => $this->input('hoursTheory', $this->hours_theory),
            'pre_requisite_id' => $this->input('preRequisiteId', $this->pre_requisite_id),
            'is_extracurricular' => $this->input('isExtracurricular', $this->is_extracurricular),
            'is_enabled' => $this->input('isEnabled', $this->is_enabled),
        ]);
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = collect($validator->errors())->mapWithKeys(function ($messages, $field) {
            return [
                Str::camel($field) => $messages[0]
            ];
        });

        throw new HttpResponseException(
            response()->json(['errors' => $errors], 422)
        );
    }
}
