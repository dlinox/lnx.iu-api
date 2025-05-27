<?php

namespace App\Modules\Course\Models;

use App\Traits\HasDataTable;
use App\Traits\HasEnabledState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Course extends Model
{
    use HasDataTable, HasEnabledState;

    protected $fillable = [
        'name',
        'code',
        'hours_practice',
        'hours_theory',
        'credits',
        'area_id',
        'module_id',
        'units',
        'curriculum_id',
        'pre_requisite_id',
        'order',
        'description',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    static $searchColumns = [
        'courses.name',
        'courses.code',
        'areas.name',
        'modules.name',
        'curriculums.name',
    ];

    //DEPRECATED
    public static function getItemsForSelect($curriculumId)
    {
        $courses = self::select(
            'courses.id as value',
            'courses.name as label',
        )
            ->join('curriculum_courses', 'courses.id', '=', 'curriculum_courses.course_id')
            ->where('curriculum_courses.curriculum_id', $curriculumId)
            ->get();

        return $courses;
    }
    //get course by curriculum 2 and module  DEPRECATED
    public static function geCurriculumCourses($curriculum_id, $module_id)
    {
        $courses = self::select(
            'courses.id as id',
            'curriculum_courses.id as curriculumCourseId',
            'courses.name as name',
            'courses.description as description',
            'courses.is_enabled as isEnabled',
            'curriculum_courses.code as code',
            'curriculum_courses.credits as credits',
            'curriculum_courses.hours_practice as hoursPractice',
            'curriculum_courses.hours_theory as hoursTheory',
            'areas.name as area',
        )
            ->join('curriculum_courses', 'courses.id', '=', 'curriculum_courses.course_id')
            ->leftJoin('areas', 'curriculum_courses.area_id', '=', 'areas.id')
            ->where('curriculum_courses.curriculum_id', $curriculum_id)
            ->where('curriculum_courses.module_id', $module_id)
            ->get();

        return $courses;
    }

    public static function getItemsByModuleForSelect($moduleId)
    {
        $courses = self::select(
            'courses.id as value',
            DB::raw("CONCAT_WS(' ', courses.code, courses.name) as label"),
        )
            ->where('courses.module_id', $moduleId)
            ->where('courses.is_enabled', true)
            ->get();

        return $courses;
    }
}
