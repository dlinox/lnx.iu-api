<?php

namespace App\Modules\Price\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Str;

class PriceStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'curriculum_id' => 'required|integer',
            //no se puede repetir el módulo para el mismo tipo de estudiante en la misma currícula
            'module_id' => 'required|integer|unique:prices,module_id,NULL,id,student_type_id,' . $this->student_type_id . ',curriculum_id,' . $this->curriculum_id,
            'student_type_id' => 'required|integer',
            'enrollment_price' => 'required|numeric',
            'presential_price' => 'required|numeric',
            'virtual_price' => 'required|numeric',
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
            'enrollment_price.required' => 'Obligatorio',
            'enrollment_price.numeric' => 'Debe ser un número',
            'presential_price.required' => 'Obligatorio',
            'presential_price.numeric' => 'Debe ser un número',
            'virtual_price.required' => 'Obligatorio',
            'virtual_price.numeric' => 'Debe ser un número',
            'is_enabled.required' => 'Obligatorio',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'curriculum_id' => $this->input('curriculumId', $this->curriculum_id),
            'module_id' => $this->input('moduleId', $this->module_id),
            'student_type_id' => $this->input('studentTypeId', $this->student_type_id),
            'enrollment_price' => $this->input('enrollmentPrice', $this->enrollment_price),
            'presential_price' => $this->input('presentialPrice', $this->presential_price),
            'virtual_price' => $this->input('virtualPrice', $this->virtual_price),
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
