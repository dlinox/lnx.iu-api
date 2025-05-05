<?php

namespace App\Modules\Teacher\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'personId' => $this->person_id,
            'documentType' => $this->document_type,
            'documentNumber' => $this->document_number,
            'name' => $this->name,
            'lastNameFather' => $this->last_name_father,
            'lastNameMother' => $this->last_name_mother,
            'gender' => $this->gender,
            'email' => $this->email,
            'phone' => $this->phone,
            'userId' => $this->user_id,
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}
