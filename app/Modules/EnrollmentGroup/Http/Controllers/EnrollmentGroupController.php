<?php

namespace App\Modules\EnrollmentGroup\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\EnrollmentGroup\Http\Resources\EnrollmentGroupDataTableItemsResource;
use App\Modules\EnrollmentGroup\Models\EnrollmentGroup;
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
            )
                ->join('courses', 'courses.id', '=', 'groups.course_id')
                ->join('areas', 'areas.id', '=', 'courses.area_id')
                ->join('modules', 'modules.id', '=', 'courses.module_id')
                ->dataTable($request, []);
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
