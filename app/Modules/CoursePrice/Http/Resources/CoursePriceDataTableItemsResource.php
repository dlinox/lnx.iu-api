<?php

namespace App\Modules\CoursePrice\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Ramsey\Uuid\Type\Decimal;

class CoursePriceDataTableItemsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'curriculumId' => $this->curriculum_id,
            'courseId' => $this->course_id,
            'course' => $this->course,
            'studentTypeId' => $this->student_type_id,
            'studentType' => $this->student_type,
            'presentialPrice' =>  (float) $this->presential_price,
            'virtualPrice' => (float) $this->virtual_price,
            'isEnabled' => $this->is_enabled,
        ];
        return parent::toArray($request);
    }
}
