<?php

namespace App\Modules\ModulePrice\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ModulePriceUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->id;
        return [
            'curriculum_id' => 'required|integer',
            'module_id' => 'required|integer|unique:module_prices,module_id,' . $id . ',id,student_type_id,' . $this->student_type_id . ',curriculum_id,' . $this->curriculum_id,
            'student_type_id' => 'required|integer',
            'price' => 'required|numeric',
            'is_enabled' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'curriculum_id.required' => 'Obligatorio',
            'module_id.required' => 'Obligatorio',
            'module_id.integer' => 'Debe ser un número entero',
            'module_id.unique' => 'Ya existe un registro con este módulo y tipo de estudiante',
            'student_type_id.required' => 'Obligatorio',
            'student_type_id.integer' => 'Debe ser un número entero',
            'price.required' => 'Obligatorio',
            'price.numeric' => 'Debe ser un número',
            'is_enabled.required' => 'Obligatorio',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'curriculum_id' => $this->input('curriculumId', $this->curriculum_id),
            'module_id' => $this->input('moduleId', $this->module_id),
            'student_type_id' => $this->input('studentTypeId', $this->student_type_id),
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
            response()->json(
                [
                    'errors' => $errors,
                    'message' => 'Error en la operación, revise los datos ingresados'
                ],
                422
            )
        );
    }
}
