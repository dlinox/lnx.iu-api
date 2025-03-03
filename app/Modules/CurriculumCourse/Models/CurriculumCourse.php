<?php

namespace App\Modules\CurriculumCourse\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CurriculumCourse extends Model
{

    use HasDataTable, HasEnabledState;

    protected $fillable = [
        'order',
        'code',
        'hours_practice',
        'hours_theory',
        'credits',
        'course_id',
        'module_id',
        'area_id',
        'curriculum_id',
        'pre_requisite_id',
        'is_extracurricular',
        'is_enabled',
    ];

    protected $casts = [
        'order' => 'integer',
        'hours_practice' => 'integer',
        'hours_theory' => 'integer',
        'credits' => 'integer',
        'course_id' => 'integer',
        'module_id' => 'integer',
        'area_id' => 'integer',
        'curriculum_id' => 'integer',
        'pre_requisite_id' => 'integer',
        'is_extracurricular' => 'boolean',
        'is_enabled' => 'boolean',
    ];

    
    static $searchColumns = [
        'courses.name',
    ];


    static function getPreRequisiteById($id)
    {

        $preRequisite = CurriculumCourse::select(
            'curriculum_courses.id',
            DB::raw("CONCAT_WS(' - ', curriculum_courses.code , courses.name) as name"),
        )
            ->join('courses', 'courses.id', '=', 'curriculum_courses.course_id')
            ->where('curriculum_courses.id', $id)
            ->first();

        return $preRequisite ? $preRequisite : null;
    }
}
