<?php

namespace App\Modules\Period\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PeriodStoreRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'year' => [
                'required',
                'integer',
                'digits:4',
            ],
            'month' => [
                'required',
                'between:1,12',
                Rule::unique('periods', 'month')->where(function ($query) {
                    return $query->where('year', $this->year);
                }),
            ],
        ];
    }

    public function messages()
    {
        return [
            'year.required' => 'Obligatorio',
            'year.integer' => 'No es un año válido',
            'year.digits' => 'El año debe tener 4 dígitos',
            'month.required' => 'Obligatorio',
            'month.between' => 'No es un mes válido',
            'month.unique' => 'El periodo ya existe',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([]);
    }
}
