<?php

namespace App\Modules\Group\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\Course\Models\Course;
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

            $items = Course::select(
                'courses.id',
                'courses.code',
                'courses.name as course',
                'areas.name as area',
                'modules.name as module',
                'courses.is_enabled',
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT( course_prices.presential_price," - ", student_types.name) SEPARATOR ",") as presential_price'),
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT( course_prices.virtual_price," - ", student_types.name) SEPARATOR ",") as virtual_price'),
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(" - ", module_prices.price, student_types_module.name) SEPARATOR ",") as module_price'),
                DB::raw('COUNT(course_prices.id) as count_prices'),
                DB::raw('COUNT(distinct groups.id) as count_groups')
            )
                ->leftJoin('groups', function ($join) use ($request) {
                    $join->on('courses.id', '=', 'groups.course_id')
                        ->where('groups.period_id', $request->filters['periodId']);
                })
                ->join('areas', 'areas.id', '=', 'courses.area_id')
                ->leftJoin('course_prices', 'courses.id', '=', 'course_prices.course_id')
                ->leftJoin('modules', 'modules.id', '=', 'courses.module_id')
                ->leftJoin('student_types', 'student_types.id', '=', 'course_prices.student_type_id')
                ->leftJoin('module_prices', 'module_prices.module_id', '=', 'modules.id')
                ->leftJoin('student_types as student_types_module', 'student_types_module.id', '=', 'module_prices.student_type_id')
                ->where('courses.curriculum_id', $request->filters['curriculumId'])
                ->groupBy('courses.id')
                ->dataTable($request, ['courses.name', 'areas.name', 'modules.name']);
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
                'groups.min_students',
                'groups.max_students',
                'groups.modality',
                'groups.course_id',
                'groups.status',
            )
                ->where('groups.course_id', $request->id)
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
                $group['course_id'] = $request->courseId;
                $group['status'] = 'ABIERTO';
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
