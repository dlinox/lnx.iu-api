<?php

namespace App\Modules\ModulePrice\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Str;

class ModulePriceStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'module_id' => 'required|integer|unique:module_prices,module_id,NULL,id,student_type_id,' . $this->student_type_id ,
            'student_type_id' => 'required|integer',
            'price' => 'required|numeric',
            'is_enabled' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
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
                    'message' => 'Error al guardar los registros, verifique los datos ingresados'
                ],
                422
            )
        );
    }
}
