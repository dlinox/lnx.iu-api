<?php

namespace App\Modules\Person\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PersonUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $id = $this->id;
        return [
            'name' => 'required|string|max:50|unique:document_types,name,' . $id,
            'is_enabled' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Obligatorio',
            'name.max' => 'Máximo de 50 caracteres',
            'name.unique' => 'Ya existe un registro con este nombre',
            'is_enabled.required' => 'Obligatorio',
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
