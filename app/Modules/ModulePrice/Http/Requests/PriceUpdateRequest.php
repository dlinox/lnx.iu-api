<?php

namespace App\Modules\Price\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PriceUpdateRequest extends FormRequest
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
            'module_id' => 'required|integer|unique:prices,module_id,' . $id . ',id,student_type_id,' . $this->student_type_id . ',curriculum_id,' . $this->curriculum_id,
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
            'name.required' => 'Obligatorio',
            'name.max' => 'Máximo de 50 caracteres',
            'name.unique' => 'Ya existe un registro con este nombre',
            'description.max' => 'Máximo de 255 caracteres',
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
                    'message' => 'Error en la operación, revise los datos ingresados'
                ],
                422
            )
        );
    }
}
