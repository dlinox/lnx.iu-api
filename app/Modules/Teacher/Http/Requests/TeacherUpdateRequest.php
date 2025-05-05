<?php

namespace App\Modules\Teacher\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;

class TeacherUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        $id = $this->id;

        return [
            'document_type_id' => 'required|exists:document_types,id',
            'document_number' => 'required|max:15|unique:teachers,document_number,' . $id,
            'name' => 'required|max:50',
            'last_name_father' => 'nullable|max:50',
            'last_name_mother' => 'nullable|max:50',
            'gender' => 'nullable|in:1,2,0',
            'email' => 'nullable|email|max:80',
            'phone' => 'nullable|max:15',
            'address' => 'nullable|max:100',
            'date_of_birth' => 'nullable',
            'is_enabled' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'Obligatorio',
            'document_type_id.required' => 'Obligatorio',
            'document_type_id.exists' => 'No existe un registro con este identificador',
            'document_number.required' => 'Obligatorio',
            'document_number.max' => 'Máximo de 15 caracteres',
            'document_number.unique' => 'Ya existe un registro con este número de documento',
            'name.required' => 'Obligatorio',
            'name.max' => 'Máximo de 50 caracteres',
            'last_name_father.max' => 'Máximo de 50 caracteres',
            'last_name_mother.max' => 'Máximo de 50 caracteres',
            'gender.in' => 'Valor no permitido',
            'email.email' => 'Formato de correo no válido',
            'email.max' => 'Máximo de 80 caracteres',
            'phone.max' => 'Máximo de 15 caracteres',
            'address.max' => 'Máximo de 100 caracteres',
            'is_enabled.required' => 'Obligatorio',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'id' => $this->id,
            'address' => isset($this->address) ? $this->address : null,
            'document_type_id' => $this->input('documentTypeId', $this->document_type_id),
            'document_number' => $this->input('documentNumber', $this->document_number),
            'last_name_father' => $this->input('lastNameFather', $this->last_name_father),
            'last_name_mother' => $this->input('lastNameMother', $this->last_name_mother),
            'date_of_birth' => $this->input('dateOfBirth', $this->date_of_birth),
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
