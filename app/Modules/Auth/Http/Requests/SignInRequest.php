<?php

namespace App\Modules\Auth\Http\Requests;

use App\Http\Requests\BaseRequest;

class SignInRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'username' => [
                'required',
            ],
            'password' => [
                'required',
                'min:4',
            ],
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'Obligatorio',
            'password.required' => 'Obligatorio',
            'password.min' => 'La contraseña debe tener al menos 4 caracteres',
        ];
    }
}
