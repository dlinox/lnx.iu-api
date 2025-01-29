<?php

namespace App\Modules\CurriculumModuleCourse\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;

class CurriculumModuleCourseUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    
    public function rules()
    {
        $id = $this->id;
        $curriculumModuleId = $this->curriculum_module_id;
        return [
            'order' => 'required|integer|unique:curriculum_module_courses,order,' . $id . ',id,curriculum_module_id,' . $curriculumModuleId,
            'code' => 'nullable|string|max:50',
            'hours_practice' => 'required|integer',
            'hours_theory' => 'required|integer',
            'credits' => 'required|integer',
            'curriculum_module_id' => 'required',
            'course_id' => 'required|unique:curriculum_module_courses,course_id,' . $id . ',id,curriculum_module_id,' . $curriculumModuleId,
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
            'curriculum_module_id.required' => 'Obligatorio',
            'course_id.required' => 'Obligatorio',
            'course_id.unique' => 'Ya existe el curso en este módulo',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'curriculum_module_id' => $this->input('curriculumModuleId', $this->curriculum_module_id),
            'course_id' => $this->input('courseId', $this->course_id),
            'hours_practice' => $this->input('hoursPractice', $this->hours_practice),
            'hours_theory' => $this->input('hoursTheory', $this->hours_theory),
            'pre_requisite_id' => $this->input('preRequisiteId', $this->pre_requisite_id),  
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