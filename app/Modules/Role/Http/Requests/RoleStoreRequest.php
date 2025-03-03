<?php

namespace App\Modules\Role\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('roles'),
            ],
            'is_enabled' => [
                'required',
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.max' => 'El nombre no debe ser mayor a 255 caracteres.',
            'name.unique' => 'El nombre ya ha sido registrado.',
            'is_enabled.required' => 'El campo is_enabled es obligatorio.',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'is_enabled' => $this->input('isEnabled', $this->is_enabled),
        ]);
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = collect($validator->errors())->map(fn($messages) => $messages[0]);

        throw new HttpResponseException(
            response()->json(['errors' => $errors], 422)
        );
    }
}
