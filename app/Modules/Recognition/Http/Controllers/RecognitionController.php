<?php

namespace App\Modules\Recognition\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Course\Models\Course;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\EnrollmentGroup\Models\EnrollmentGroup;
use App\Modules\Recognition\Http\Resources\RecognitionDataTableItemsResource;
use App\Modules\Recognition\Models\Recognition;
use Illuminate\Support\Facades\DB;

class RecognitionController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $items = Recognition::select(
                'recognitions.id',
                'modules.name as module',
                'courses.name as course',
                'courses_recognition.name as course_recognition',
                DB::raw("CONCAT_WS(' ', students.name, students.last_name_father, students.last_name_mother) as student"),
                'recognitions.observation',
                'recognitions.created_at',
            )
                ->join('modules', 'modules.id', 'recognitions.module_id')
                ->join('enrollment_groups', 'enrollment_groups.id', 'recognitions.enrollment_group_id')
                ->join('groups', 'groups.id', 'enrollment_groups.group_id')
                ->join('courses', 'courses.id', 'groups.course_id')
                ->join('students', 'students.id', 'recognitions.student_id')
                ->join('courses as courses_recognition', 'courses_recognition.id', 'recognitions.course_recognition_id')
                ->dataTable($request, []);
            RecognitionDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function store(Request $request)
    {
        try {
            $recognition = Recognition::create([
                'module_id' => $request->moduleId,
                'enrollment_group_id' => $request->courseId,
                'course_recognition_id' => $request->courseRecognitionId,
                'student_id' => $request->studentId,
            ]);

            return ApiResponse::success($recognition);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al guardar el registro');
        }
    }

    public function destroy(Request $request)
    {
        try {
            $recognition = Recognition::find($request->id);
            if (!$recognition) {
                return ApiResponse::error('No se encontró el registro', 'Error al eliminar el registro');
            }
            $recognition->delete();
            return ApiResponse::success($recognition);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al eliminar el registro');
        }
    }

    public function getModuleEnrollmentsForSelect(Request $request)
    {
        try {
            $items = Enrollment::select(
                'modules.id as value',
                'modules.name as label',
            )->distinct()
                ->join('modules', 'modules.id', 'enrollments.module_id')
                ->where('modules.is_extracurricular', 0)
                ->where('enrollments.student_id', $request->studentId)
                ->get();

            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los datos');
        }
    }

    public function getCourseByModuleForSelect(Request $request)
    {
        try {
            $items = Course::select(
                'courses.id as value',
                DB::raw("CONCAT_WS(' ', courses.name, IFNULL(MAX(CASE WHEN enrollment_groups.student_id = ? THEN enrollment_grades.grade END), '')) as label")
            )
                ->join('modules', 'modules.id', 'courses.module_id')
                ->leftJoin('groups', 'groups.course_id', 'courses.id')
                ->leftJoin('enrollment_groups', 'enrollment_groups.group_id', 'groups.id')
                ->leftJoin('enrollment_grades', 'enrollment_grades.enrollment_group_id', 'enrollment_groups.id')
                ->where('modules.id', $request->moduleId)
                ->where('courses.is_enabled', 1)
                ->groupBy('courses.id', 'courses.name')
                ->setBindings([$request->studentId], 'select') // Necesario para el binding en CASE WHEN
                ->get();
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los datos');
        }
    }


    public function getExtracurricularsForSelect(Request $request)
    {
        try {
            $items = EnrollmentGroup::select(
                'enrollment_groups.id as value',
                DB::raw("CONCAT_WS(' ', courses.name, IFNULL(MAX(enrollment_grades.grade), '')) as label")
            )
                ->join('groups', 'groups.id', '=', 'enrollment_groups.group_id')
                ->join('courses', 'courses.id', '=', 'groups.course_id')
                ->join('enrollment_grades', 'enrollment_grades.enrollment_group_id', 'enrollment_groups.id')
                ->where('enrollment_groups.student_id', $request->studentId)
                ->where('enrollment_groups.special_enrollment', 1)
                ->where('enrollment_grades.grade', '>', 10)
                ->where('courses.is_enabled', 1)
                ->groupBy('enrollment_groups.id', 'courses.name')
                ->whereNotIn('enrollment_groups.id', function ($query) use ($request) {
                    $query->select('recognitions.enrollment_group_id')
                        ->from('recognitions')
                        ->where('recognitions.student_id', $request->studentId);
                })
                ->get();

            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
