<?php

namespace App\Modules\AcademicSupervision\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\AcademicSupervision\Http\Resources\AcademicSupervisionDataTableItemsResource;
use App\Modules\AcademicSupervision\Models\AcademicSupervision;
use App\Modules\Group\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcademicSupervisionController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {

            $items = AcademicSupervision::select(
                'academic_supervisions.id',
                'groups.name as group',
                'groups.id as group_id',
                DB::raw('CONCAT(periods.year, " ", months.name) as period'),
                'courses.name as course',
                DB::raw('CONCAT(teachers.name, " ", teachers.last_name_father, " ", teachers.last_name_mother) as teacher'),
                'teachers.id as teacher_id',
                'academic_supervisions.type',
                'academic_supervisions.observations',
            )
                ->join('groups', 'academic_supervisions.group_id', '=', 'groups.id')
                ->join('periods', 'groups.period_id', '=', 'periods.id')
                ->join('courses', 'groups.course_id', '=', 'courses.id')
                ->join('teachers', 'academic_supervisions.teacher_id', '=', 'teachers.id')
                ->join('months', 'periods.month', '=', 'months.id')
                ->dataTable($request);
            AcademicSupervisionDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function save(Request $request)
    {
        try {

            $group = Group::find($request->groupId);
            if (!$group) return ApiResponse::error('', 'Grupo no encontrado');

            if ($request->id) {
                $item = AcademicSupervision::find($request->id);

                if ($item->teacher_id != $group->teacher_id) {
                    $item->teacher_id = $group->teacher_id;
                }

                $item->group_id = $request->groupId;
                $item->type = $request->type;
                $item->user_id = Auth::user()->id;
                $item->observations = $request->observations;
                $item->save();
            } else {
                AcademicSupervision::create([
                    'group_id' => $request->groupId,
                    'type' => $request->type,
                    'teacher_id' => $group->teacher_id,
                    'user_id' => Auth::user()->id,
                    'observations' => $request->observations,
                ]);
            }
            return ApiResponse::success(null, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $item = AcademicSupervision::find($request->id);
            $item->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getActiveGroups(Request $request)
    {
        try {
            $groups = Group::select(
                'groups.id as value',
                DB::raw('CONCAT(groups.name, " - ", teachers.name, " ", teachers.last_name_father, " ", teachers.last_name_mother) as label'),
            )
                ->distinct()
                ->join('teachers', 'groups.teacher_id', '=', 'teachers.id')
                ->whereIn('groups.status', ['ABIERTO', 'CERRADO'])
                ->get();

            return ApiResponse::success($groups);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los grupos activos');
        }
    }
}
