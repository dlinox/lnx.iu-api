<?php

namespace App\Modules\Module\Http\Requests;

use App\Http\Requests\BaseRequest;
use Faker\Provider\Base;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Support\Str;

class ModuleStoreRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'level' => 'required|integer',
            'curriculum_id' => 'required|integer|exists:curriculums,id',
            'code' => 'required|string|max:10|unique:modules,code,NULL,id,curriculum_id,' . $this->curriculum_id,
            'name' => 'required|string|max:50|unique:modules,name,NULL,id,curriculum_id,' . $this->curriculum_id,
            'description' => 'nullable|max:255',
            'is_extracurricular' => 'required|boolean',
            'is_enabled' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'level.required' => 'Obligatorio',
            'level.integer' => 'No es un nivel válido',
            'curriculum_id.required' => 'Obligatorio',
            'curriculum_id.integer' => 'No es un identificador válido',
            'curriculum_id.exists' => 'No existe un registro con este identificador',
            'name.required' => 'Obligatorio',
            'name.max' => 'Máximo de 50 caracteres',
            'name.unique' => 'Ya existe un registro con este nombre',
            'code.required' => 'Obligatorio',
            'code.max' => 'Máximo de 10 caracteres',
            'code.unique' => 'Ya existe un registro con este código',
            'description.max' => 'Máximo de 255 caracteres',
            'is_extracurricular.required' => 'Obligatorio',
            'is_enabled.required' => 'Obligatorio',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'curriculum_id' => $this->input('curriculumId', $this->curriculum_id),
            'is_extracurricular' => $this->input('isExtracurricular', $this->is_extracurricular),
            'is_enabled' => $this->input('isEnabled', $this->is_enabled),
        ]);
    }

}
