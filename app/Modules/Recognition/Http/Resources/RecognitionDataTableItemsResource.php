<?php

namespace App\Modules\Recognition\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecognitionDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'module' => $this->module,
            'course' => $this->course,
            'courseRecognition' => $this->course_recognition,
            'student' => $this->student,
            'observation' => $this->observation,
            'createdAt' => $this->created_at,
        ];
        return parent::toArray($request);
    }
}
