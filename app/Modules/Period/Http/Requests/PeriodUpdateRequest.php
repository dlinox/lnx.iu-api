<?php

namespace App\Modules\Period\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class PeriodUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->id;

        return [
            'year' => [
                'required',
                'integer',
                'digits:4',
            ],
            'month' => [
                'required',
                'between:1,12',
                Rule::unique('periods', 'month')->where(function ($query) use ($id) {
                    return $query->where('year', $this->year)->where('id', '!=', $id);
                }),
            ],
            'status' => [
                'required',
                Rule::in([
                    "PENDIENTE",
                    "MATRICULA",
                    "EN CURSO",
                    "FINALIZADO",
                    "CANCELADO",
                ]),
            ],
        ];
    }

    public function messages()
    {
        return [
            'year.required' => 'Obligatorio',
            'year.digits' => 'El año debe tener 4 dígitos',
            'year.integer' => 'No es un año válido',
            'month.required' => 'Obligatorio',
            'month.between' => 'No es un mes válido',
            'month.unique' => 'El periodo ya existe',
            'status.required' => 'Obligatorio',
            'status.in' => 'No es un estado válido',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([]);
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = collect($validator->errors())->map(fn($messages) => $messages[0]);

        throw new HttpResponseException(
            response()->json(['errors' => $errors], 422)
        );
    }
}
