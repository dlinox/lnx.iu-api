<?php

namespace App\Modules\Enrollment\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Str;

class ModuleStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'student_id' => 'required|int',
            'module_id' => 'required|int',
        ];
    }

    public function messages()
    {
        return [
            'student_id.required' => 'El estudiante es requerido',
            'student_id.int' => 'El estudiante no es válido',
            'module_id.required' => 'El módulo es requerido',
            'module_id.int' => 'El módulo no es válido',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'student_id' => $this->input('studentId', $this->student_id),
            'module_id' => $this->input('moduleId', $this->module_id),
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
                    'message' => 'Error al guardar los registros, verifique los datos ingresados' . $errors
                ],
                422
            )
        );
    }
}
