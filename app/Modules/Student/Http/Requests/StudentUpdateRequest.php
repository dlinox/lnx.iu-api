<?php

namespace App\Modules\Student\Http\Requests;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StudentUpdateRequest extends BaseRequest
{

    public function rules()
    {
        $id = $this->id;

        return [
            'id' => 'required',
            'document_type_id' => 'required|exists:document_types,id',
            'document_number' => 'required|max:15|unique:students,document_number,' . $id,
            'name' => 'required|max:50',
            'last_name_father' => 'nullable|max:50',
            'last_name_mother' => 'nullable|max:50',
            'gender' => 'nullable|in:1,2',
            'email' => [
                'required',
                'email',
                'max:80',
                Rule::unique('students', 'email')->ignore($this->id),
            ],
            'phone' => 'nullable|max:15',
            'address' => 'nullable|max:100',
            'student_type_id' => 'required|exists:student_types,id',
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
            'email.required' => 'Obligatorio',
            'email.email' => 'Formato de correo no válido',
            'email.max' => 'Máximo de 80 caracteres',
            'email.unique' => 'Ya existe un registro con este correo',
            'phone.max' => 'Máximo de 15 caracteres',
            'address.max' => 'Máximo de 100 caracteres',
            'student_type_id.required' => 'Obligatorio',
            'student_type_id.exists' => 'No existe un registro con este identificador',
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
            'student_type_id' => $this->input('studentTypeId', $this->student_type_id),
            'date_of_birth' => $this->input('dateOfBirth', $this->date_of_birth),
            'is_enabled' => $this->input('isEnabled', $this->is_enabled),
        ]);
    }
}
