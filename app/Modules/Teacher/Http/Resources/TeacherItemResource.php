<?php

namespace App\Modules\Teacher\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'personId' => $this->person_id,
            'documentTypeId' => $this->document_type_id,
            'documentNumber' => $this->document_number,
            'name' => $this->name,
            'lastNameFather' => $this->last_name_father,
            'lastNameMother' => $this->last_name_mother,
            'gender' => $this->gender,
            'email' => $this->email,
            'phone' => $this->phone,
            'dateOfBirth' => $this->date_of_birth,
            'isEnabled' => $this->is_enabled
        ];
        return parent::toArray($request);
    }
}
