<?php

namespace App\Modules\Role\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RoleUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->id;

        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique('roles')->ignore($id),
            ],
            'is_enabled' => [
                'required',
                'boolean',
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El rol es obligatorio.',
            'name.max' => 'El  rol no debe ser mayor a 255 caracteres.',
            'name.unique' => 'El rol ya ha sido registrado.',
            'is_enabled.required' => 'El campo is_enabled es obligatorio.',
            'is_enabled.boolean' => 'El campo is_enabled debe ser un valor booleano.',

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
