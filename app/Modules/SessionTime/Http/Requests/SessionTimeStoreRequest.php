<?php

namespace App\Modules\SessionTime\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Str;
class SessionTimeStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'start_time' => [
                'required',
                'max:35',
                'regex:/^(0?[1-9]|1[0-2]):[0-5][0-9] (am|pm).$/i',
            ],

            'end_time' => [
                'required',
                'max:35',
                'regex:/^(0?[1-9]|1[0-2]):[0-5][0-9] (am|pm).$/i',
            ],
            'is_enabled' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [

            'start_time.required' => 'Obligatorio',
            'start_time.regex' => 'Formato incorrecto(10:00 am.)',
            'end_time.required' => 'Obligatorio',
            'end_time.regex' => 'Formato incorrecto ejemplo: (10:00 am.)',
            'is_enabled.required' => 'Obligatorio',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'start_time' => $this->input('startTime', $this->start_time),
            'end_time' => $this->input('endTime', $this->end_time),
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
            response()->json(['errors' => $errors], 422)
        );
    }
}
