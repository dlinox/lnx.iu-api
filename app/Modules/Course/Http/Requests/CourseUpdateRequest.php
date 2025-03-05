<?php

namespace App\Modules\Course\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CourseUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->id;
        return [
            'order' => 'required|integer',
            'code' => 'required|string|max:10|unique:courses,code,' . $id . ',id,name,' . $this->name . ',module_id,' . $this->module_id . ',curriculum_id,' . $this->curriculum_id,
            'name' => 'required|string|max:50',
            'hours_practice' => 'required|integer',
            'hours_theory' => 'required|integer',
            'credits' => 'required|integer',
            'units' => 'required|integer',
            'area_id' => 'required|integer|exists:areas,id',
            'module_id' => 'required|integer|exists:modules,id',
            'curriculum_id' => 'required|integer|exists:curriculums,id',
            'pre_requisite_id' => 'nullable|integer|exists:courses,id',
            'description' => 'nullable|max:255',
            'is_enabled' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'order.required' => 'Obligatorio',
            'code.required' => 'Obligatorio',
            'code.max' => 'Máximo de 10 caracteres',
            'code.unique' => 'Ya existe un registro con este código/nombre/modulo/curriculum',
            'name.required' => 'Obligatorio',
            'hours_practice.required' => 'Obligatorio',
            'hours_theory.required' => 'Obligatorio',
            'credits.required' => 'Obligatorio',
            'units.required' => 'Obligatorio',
            'area_id.required' => 'Obligatorio',
            'module_id.required' => 'Obligatorio',
            'curriculum_id.required' => 'Obligatorio',
            'pre_requisite_id.required' => 'Obligatorio',
            'description.required' => 'Obligatorio',
            'is_enabled.required' => 'Obligatorio',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'hours_practice' => $this->input('hoursPractice', $this->hours_practice),
            'hours_theory' => $this->input('hoursTheory', $this->hours_theory),
            'area_id' => $this->input('areaId', $this->area_id),
            'module_id' => $this->input('moduleId', $this->module_id),
            'curriculum_id' => $this->input('curriculumId', $this->curriculum_id),
            'pre_requisite_id' => $this->input('preRequisiteId', $this->pre_requisite_id),
            'is_enabled' => $this->input('isEnabled', $this->is_enabled),
        ]);
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = collect($validator->errors())->map(fn($messages) => $messages[0]);

        throw new HttpResponseException(
            response()->json(['errors' => $errors], 422)
        );
    }
}
