<?php

namespace App\Modules\Curriculum\Http\Requests;

use App\Http\Requests\BaseRequest;

class CurriculumUpdateRequest extends BaseRequest
{
    public function rules()
    {
        $id = $this->id;
        return [
            'name' => 'required|string|max:50|unique:curriculums,name,' . $id,
            'grading_model' => 'required|in:1,2',
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
            'grading_model' => $this->input('gradingModel', $this->grading_model),
            'is_enabled' => $this->input('isEnabled', $this->is_enabled),
        ]);
    }
}
