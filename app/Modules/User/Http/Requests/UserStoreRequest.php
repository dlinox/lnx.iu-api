<?php

namespace App\Modules\User\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UserStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $level = $this->level ?? null;

        $modelStudent = [
            'required',
            'exists:students,id',
            Rule::unique('users')->where(function ($query) use ($level) {
                return $query->where('model_type', $level);
            }),
        ];

        $modelTeacher = [
            'required',
            'exists:teachers,id',
            Rule::unique('users')->where(function ($query) use ($level) {
                return $query->where('model_type', $level);
            }),
        ];

        return [
            'model_id' => $level == 'student' ? $modelStudent : ($level == 'teacher' ? $modelTeacher : ['nullable']),
            'name' => [
                'required',
                'max:255',
            ],
            'username' => [
                'required',
                'max:255',
                Rule::unique('users'),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users'),
            ],
            'role' => [
                'required',
                'exists:roles,id',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
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
            'username.required' => 'El nombre de usuario es obligatorio.',
            'username.max' => 'El nombre de usuario no debe ser mayor a 255 caracteres.',
            'username.unique' => 'El nombre de usuario ya ha sido registrado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'email.unique' => 'El correo electrónico ya ha sido registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser una cadena de texto.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'role.required' => 'El rol es obligatorio.',
            'role.exists' => 'El rol no existe.',
            'is_enabled.required' => 'El campo is_enabled es obligatorio.',

            'model_id.required' => 'El campo model_id es obligatorio.',
            'model_id.exists' => $this->level == 'student' ? 'El estudiante no existe.' :  'El docente no existe.',
            'model_id.unique' => $this->level == 'student' ? 'El estudiante ya tiene una cuenta.' : 'El docente ya tiene una cuenta.',

        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'model_id' => $this->input('modelId', $this->model_id),
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
