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
use App\Modules\Laboratory\Models\Laboratory;
use App\Modules\Period\Models\Period;
use App\Modules\Schedule\Models\Schedule;
use App\Modules\Teacher\Models\Teacher;
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
                ->leftJoin('areas', 'areas.id', '=', 'courses.area_id')
                ->leftJoin('course_prices', 'courses.id', '=', 'course_prices.course_id')
                ->leftJoin('modules', 'modules.id', '=', 'courses.module_id')
                ->leftJoin('student_types', 'student_types.id', '=', 'course_prices.student_type_id')
                ->leftJoin('module_prices', 'module_prices.module_id', '=', 'modules.id')
                ->leftJoin('student_types as student_types_module', 'student_types_module.id', '=', 'module_prices.student_type_id')
                ->where('courses.curriculum_id', $request->filters['curriculumId'])
                ->groupBy('courses.id')
                ->orderBy('modules.id', 'asc')
                ->orderBy('courses.name', 'asc')
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
                if ($group['id'] == null) $group['status'] = 'ABIERTO';

                $groupModel = Group::updateOrCreate(
                    ['id' => $group['id']],
                    $group
                );
                $groupModel->schedules()->delete();

                foreach ($group['schedule']['days'] as $key => $day) {
                    $groupModel->schedules()->create([
                        'day' => $day,
                        'start_hour' => $group['schedule']['start_hour'],
                        'end_hour' => $group['schedule']['end_hour'],
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

    //clone
    public function clone(Request $request)
    {
        try {
            $enrollments = Group::where('groups.period_id', $request->periodId)
                ->join('courses', 'courses.id', '=', 'groups.course_id')
                ->join('enrollment_groups', 'enrollment_groups.group_id', '=', 'groups.id')
                ->where('courses.curriculum_id', $request->curriculumId)
                ->exists();

            if ($enrollments) {
                return ApiResponse::error(null, 'No se puede clonar los registros, ya que existen matriculas asociadas a los grupos');
            }

            $groupIds = Group::where('period_id', $request->periodId)
                ->join('courses', 'courses.id', '=', 'groups.course_id')
                ->where('courses.curriculum_id', $request->curriculumId)
                ->pluck('groups.id')
                ->toArray();


            DB::beginTransaction();

            Schedule::whereIn('group_id', $groupIds)->delete();
            Group::whereIn('id', $groupIds)->delete();

            $groups = Group::select('groups.*')
                ->where('period_id', $request->periodReferenceId)
                ->join('courses', 'courses.id', '=', 'groups.course_id')
                ->where('courses.curriculum_id', $request->curriculumId)
                ->get();

            foreach ($groups as $group) {
                $clone = Group::create([
                    'name' => $group->name,
                    'teacher_id' => null,
                    'laboratory_id' => $group->laboratory_id,
                    'min_students' => $group->min_students,
                    'max_students' => $group->max_students,
                    'modality' => $group->modality,
                    'course_id' => $group->course_id,
                    'status' => 'ABIERTO',
                    'period_id' => $request->periodId,
                ]);

                $schedules = Schedule::where('group_id', $group->id)->get();
                foreach ($schedules as $schedule) {
                    Schedule::create([
                        'group_id' => $clone->id,
                        'day' => $schedule->day,
                        'start_hour' => $schedule->start_hour,
                        'end_hour' => $schedule->end_hour,
                    ]);
                }
            }
            DB::commit();
            return ApiResponse::success($groupIds, 'Registros clonados correctamente', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage(), 'Error al clonar los registros');
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

    // getTeachers
    public function getTeachers(Request $request)
    {
        try {
            $teachers = Teacher::select(
                'teachers.id as value',
                DB::raw('CONCAT(teachers.name, " ", teachers.last_name_father, " ", teachers.last_name_mother) as label'),
            )
                ->where('teachers.is_enabled', true)
                ->get();
            return ApiResponse::success($teachers);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    //getLaboratories
    public function getLaboratories(Request $request)
    {
        try {
            $laboratories = Laboratory::select(
                'laboratories.id as value',
                DB::raw('CONCAT(laboratories.type, "-", laboratories.name) as label'),
            )
                ->where('laboratories.is_enabled', true)
                ->get();
            return ApiResponse::success($laboratories);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    //group/save-status
    public function saveStatus(Request $request)
    {
        try {
            $group = Group::find($request->id);
            if (!$group) return ApiResponse::error(null, 'No se encontró el registro');

            $group->teacher_id = $request->teacherId;
            $group->laboratory_id = $request->laboratoryId;
            $group->status = $request->status;

            $group->save();
            return ApiResponse::success(null, 'Estado actualizado correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al actualizar el estado');
        }
    }

    public function getByModuleAndPeriod(Request $request)
    {
        try {
            $items = Group::select(
                'groups.id',
                'groups.name',
                'courses.name as course',
                'groups.status'
            )
                ->join('courses', 'courses.id', '=', 'groups.course_id')
                ->where('courses.module_id', $request->moduleId)
                ->where('groups.period_id', $request->periodId)
                ->get()->map(function ($item) {
                    $item->schedule = Schedule::byGroup($item->id);
                    return $item;
                });
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
