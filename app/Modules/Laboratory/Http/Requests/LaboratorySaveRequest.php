<?php

namespace App\Modules\Laboratory\Http\Requests;

use App\Http\Requests\BaseRequest;

class LaboratorySaveRequest extends BaseRequest
{

    public function rules()
    {
        $id = $this->id ? $this->id : null;
        return [
            'name' => 'required|string|max:50|unique:laboratories,name,' . $id,
            'virtual_link' => 'nullable|string|max:255',
            'type' => 'required|in:LABORATORIO,VIRTUAL',
            'is_enabled' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Obligatorio',
            'name.max' => 'Máximo de 50 caracteres',
            'name.unique' => 'Ya existe un registro con este nombre',
            'virtual_link.max' => 'Máximo de 255 caracteres',
            'type.required' => 'Obligatorio',
            'type.in' => 'El tipo debe ser LABORATORIO o VIRTUAL',
            'is_enabled.required' => 'Obligatorio',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'is_enabled' => $this->input('isEnabled', $this->is_enabled),
            'virtual_link' => $this->input('virtualLink', $this->virtual_link),
        ]);
    }
}
