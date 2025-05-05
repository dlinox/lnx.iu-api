<?php

namespace App\Modules\Student\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'code' => $this->code,
            'documentType' => $this->document_type,
            'documentNumber' => $this->document_number,
            'name' => $this->name,
            'lastNameFather' => $this->last_name_father,
            'lastNameMother' => $this->last_name_mother,
            'gender' => $this->gender,
            'email' => $this->email,
            'phone' => $this->phone,
            'studentType' => $this->student_type,
            'userId' => $this->user_id,
            'isEnabled' => $this->is_enabled
        ];
        return parent::toArray($request);
    }
}
