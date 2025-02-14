<?php

namespace App\Modules\Group\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\CurriculumCourse\Models\CurriculumCourse;
use App\Modules\Group\Http\Requests\GroupSaveRequest;
use App\Modules\Group\Models\Group;
use App\Modules\Group\Http\Resources\GroupDataTableItemsResource;
use App\Modules\Group\Http\Resources\GroupFormItemResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {

            $items = CurriculumCourse::select(
                'curriculum_courses.id',
                'curriculum_courses.code',
                'courses.name as course',
                'areas.name as area',
                'modules.name as module',
                'curriculum_courses.is_enabled',
                DB::raw('COUNT(groups.id) as count_groups')
            )
                ->leftJoin('groups', function ($join) use ($request) {
                    $join->on('curriculum_courses.id', '=', 'groups.curriculum_course_id')
                        ->where('groups.period_id', $request->filters['periodId']);
                })
                ->join('courses', 'courses.id', '=', 'curriculum_courses.course_id')
                ->join('areas', 'areas.id', '=', 'curriculum_courses.area_id')
                ->leftJoin('modules', 'modules.id', '=', 'curriculum_courses.module_id')
                ->where('curriculum_courses.curriculum_id', $request->filters['curriculumId'])
                ->groupBy('curriculum_courses.id')
                ->dataTable($request);
            GroupDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function loadForm(Request $request)
    {
        try {
            $items = Group::select(
                'groups.id',
                'groups.name',
                'groups.teacher_id',
                'groups.laboratory_id',
                'groups.modality',
                'groups.curriculum_course_id',
                'groups.is_enabled',
            )
                ->where('groups.curriculum_course_id', $request->id)
                ->where('groups.period_id', $request->periodId)
                ->with('schedules')
                ->get();

            return ApiResponse::success(GroupFormItemResource::collection($items));
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function save(GroupSaveRequest $request)
    {
        try {
            $data =  $request->validated();
            DB::beginTransaction();
            $groups = $data['groups'];
            foreach ($groups as $group) {
                $group['period_id'] = $request->periodId;
                $group['curriculum_course_id'] = $request->curriculumCourseId;
                $groupModel = Group::updateOrCreate(
                    ['id' => $group['id']],
                    $group
                );
                $groupModel->schedules()->delete();
                foreach ($group['schedules'] as $schedule) {
                    $groupModel->schedules()->create([
                        'day' => $schedule['day'],
                        'start_hour' => Carbon::createFromTimestampMs($schedule['start_hour'])->format('H:i:s'),
                        'end_hour' => Carbon::createFromTimestampMs($schedule['end_hour'])->format('H:i:s'),
                    ]);
                }
            }
            DB::commit();
            return ApiResponse::success($data, 'Registros creados correctamente', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage(), 'Error al guardar los registros, verifique los datos ingresados');
        }
    }

    public function destroy(Request $request)
    {
        try {

            $item = Group::find($request->id);
            //TODO: Validar si el grupo tiene matriculas
            $item->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al eliminar el registro');
        }
    }

    public function getItemsForSelect(Request $request)
    {
        try {
            $item = Group::select('id as value', 'name as label')->enabled()->get();
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
