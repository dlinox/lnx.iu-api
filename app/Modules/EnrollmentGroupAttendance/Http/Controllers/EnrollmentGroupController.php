<?php

namespace App\Modules\EnrollmentGroup\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\EnrollmentGroup\Http\Resources\EnrollmentGroupDataTableItemsResource;
use App\Modules\Group\Models\Group;
use Illuminate\Support\Facades\DB;

class EnrollmentGroupController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {

            $items = Group::select(
                'groups.id as id',
                'groups.name as group',
                'groups.modality as modality',
                'groups.min_students as minStudents',
                'groups.max_students as maxStudents',
                'groups.status as status',
                'modules.name as module',
                'areas.name as area',
                'courses.name as course',
                'teachers.id as teacher_id',
                DB::raw('CONCAT_WS(" ", teachers.name, teachers.last_name_father, teachers.last_name_mother) as teacher'),
                'laboratories.id as laboratory_id',
                'laboratories.name as laboratory',
            )
                ->join('courses', 'courses.id', '=', 'groups.course_id')
                ->join('areas', 'areas.id', '=', 'courses.area_id')
                ->join('modules', 'modules.id', '=', 'courses.module_id')
                ->leftJoin('teachers', 'teachers.id', '=', 'groups.teacher_id')
                ->leftJoin('laboratories', 'laboratories.id', '=', 'groups.laboratory_id')
                ->where('groups.period_id', $request->filters['periodId'])
                ->orderBy('groups.period_id', 'desc')
                ->dataTable($request, [
                    'groups.name',
                    'groups.modality',
                    'groups.status',
                    'modules.name',
                    'areas.name',
                    'courses.name'
                ]);
            EnrollmentGroupDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }
    public function changeStatusGroup(Request $request)
    {
        try {
            $group = Group::find($request->id);
            if (!$group) return ApiResponse::error(null, 'No se encontró el registro');
            $group->status = $request->status;
            $group->save();
            return ApiResponse::success(null, 'Estado actualizado correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al actualizar el estado');
        }
    }
}
