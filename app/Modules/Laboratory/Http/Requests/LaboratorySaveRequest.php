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
            'device_count' => 'required|integer',
            'device_detail' => 'nullable|max:150',
            'is_enabled' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Obligatorio',
            'name.max' => 'Máximo de 50 caracteres',
            'name.unique' => 'Ya existe un registro con este nombre',
            'device_count.required' => 'Obligatorio',
            'device_count.integer' => 'Debe ser un número entero',
            'is_enabled.required' => 'Obligatorio',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'is_enabled' => $this->input('isEnabled', $this->is_enabled),
            'device_count' => $this->input('deviceCount', $this->device_count),
            'device_detail' => $this->input('deviceDetail', $this->device_detail),
        ]);
    }
}
