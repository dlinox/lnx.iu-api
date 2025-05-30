<?php

namespace App\Modules\Student\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'documentTypeId' => $this->document_type_id,
            'documentNumber' => $this->document_number,
            'name' => $this->name,
            'lastNameFather' => $this->last_name_father,
            'lastNameMother' => $this->last_name_mother,
            'gender' => $this->gender,
            'email' => $this->email,
            'phone' => $this->phone,
            'studentTypeId' => $this->student_type_id,
            'dateOfBirth' => $this->date_of_birth,
            'isEnabled' => $this->is_enabled
        ];
        return parent::toArray($request);
    }
}
