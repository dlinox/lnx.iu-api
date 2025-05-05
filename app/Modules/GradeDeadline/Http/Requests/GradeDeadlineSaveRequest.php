<?php

namespace App\Modules\GradeDeadline\Http\Requests;

use App\Http\Requests\BaseRequest;

class GradeDeadlineSaveRequest extends BaseRequest
{
    public function rules()
    {
        $id = $this->id ?? null;

        return [
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'observations' => 'nullable|string',
            'period_id' => 'required|exists:periods,id',
        ];
    }

    public function messages()
    {
        return [
            'start_date.required' => 'Obligatorio',
            'start_date.date' => 'Fecha inválida',
            'start_date.before' => 'Debe ser anterior a la fecha de fin',
            'end_date.required' => 'Obligatorio',
            'end_date.after' => 'Debe ser posterior a la fecha de inicio',
            'end_date.date' => 'Fecha inválida',
            'observations.string' => 'Debe ser una cadena de texto',
            'period_id.required' => 'Obligatorio',
            'period_id.exists' => 'El periodo no existe',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'start_date' => $this->input('startDate', $this->start_date),
            'end_date' => $this->input('endDate', $this->end_date),
            'period_id' => $this->input('periodId', $this->period_id),
        ]);
    }
}
