<?php

namespace App\Modules\CurriculumModuleCourse\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CurriculumModuleCourse extends Model
{

    use HasDataTable, HasEnabledState;

    protected $fillable = [
        'order',
        'code',
        'hours_practice',
        'hours_theory',
        'credits',
        'course_id',
        'curriculum_module_id',
        'pre_requisite_id',
        'is_enabled',
    ];

    protected $casts = [
        'order' => 'integer',
        'hours_practice' => 'integer',
        'hours_theory' => 'integer',
        'credits' => 'integer',
        'course_id' => 'integer',
        'curriculum_module_id' => 'integer',
        // 'pre_requisite_id' => 'integer',
        'is_enabled' => 'boolean',
    ];


    static function getPreRequisiteById($id)
    {

        $preRequisite = CurriculumModuleCourse::select(
            'curriculum_module_courses.id',
            DB::raw("CONCAT_WS(' - ', curriculum_module_courses.code , courses.name) as name"),
        )
            ->join('courses', 'courses.id', '=', 'curriculum_module_courses.course_id')
            ->where('curriculum_module_courses.id', $id)
            ->first();

        return $preRequisite ? $preRequisite : null;
    }
}
