<?php

namespace App\Modules\CurriculumModule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Str;

class CurriculumModuleStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $curriculumId = $this->curriculum_id;
        return [
            'order' => 'required|integer|unique:curriculum_modules,order,NULL,id,curriculum_id,' . $curriculumId,
            'area_id' => 'required',
            'module_id' => 'nullable|unique:curriculum_modules,module_id,NULL,id,curriculum_id,' . $curriculumId,
            'curriculum_id' => 'required',
            'is_extracurricular' => 'boolean',
            'is_enabled' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'order.required' => 'Obligatorio',
            'order.integer' => 'Debe ser un número entero',
            'order.unique' => 'Ya existe un registro con este orden',
            'module_id.unique' => 'Ya existe el modulo en este plan de estudios',
            'area_id.required' => 'Obligatorio',
            'curriculum_id.required' => 'Obligatorio',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'is_extracurricular' => $this->input('isExtracurricular', $this->is_extracurricular),
            'is_enabled' => $this->input('isEnabled', $this->is_enabled),
            'area_id' => $this->input('areaId', $this->area_id),
            'module_id' => $this->input('moduleId', $this->module_id),
            'curriculum_id' => $this->input('curriculumId', $this->curriculum_id),
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
