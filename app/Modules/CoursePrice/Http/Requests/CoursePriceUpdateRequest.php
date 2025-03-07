<?php

namespace App\Modules\CoursePrice\Http\Requests;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CoursePriceUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->id;
        return [
            'course_id' => 'required|integer|unique:course_prices,course_id,' . $id . ',id,student_type_id,' . $this->student_type_id,
            'student_type_id' => 'required|integer',
            'presential_price' => 'required|numeric',
            'virtual_price' => 'required|numeric',
            'is_enabled' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'course_id.required' => 'Obligatorio',
            'course_id.integer' => 'Debe ser un número entero',
            'course_id.unique' => 'Ya existe un registro con estos datos',
            'student_type_id.required' => 'Obligatorio',
            'student_type_id.integer' => 'Debe ser un número entero',
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
            'course_id' => $this->input('courseId', $this->course_id),
            'student_type_id' => $this->input('studentTypeId', $this->student_type_id),
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
