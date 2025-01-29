<?php

namespace App\Modules\CurriculumModuleCourse\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\CurriculumModuleCourse\Http\Requests\CurriculumModuleCourseStoreRequest;
use App\Modules\CurriculumModuleCourse\Http\Requests\CurriculumModuleCourseUpdateRequest;
use App\Modules\CurriculumModuleCourse\Http\Resources\CurriculumModuleCourseDataTableItemsResource;
use App\Modules\CurriculumModulecourse\Http\Resources\CurriculumModuleCourseItemResource;
use App\Modules\CurriculumModuleCourse\Models\CurriculumModuleCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurriculumModuleCourseController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $items = CurriculumModuleCourse::select(
                'curriculum_module_courses.id',
                'curriculum_module_courses.order',
                'curriculum_module_courses.code',
                'curriculum_module_courses.hours_practice',
                'curriculum_module_courses.hours_theory',
                'curriculum_module_courses.credits',
                'courses.name as course',
                'curriculum_module_courses.pre_requisite_id',
                'curriculum_module_courses.is_enabled',
            )->join('courses', 'courses.id', '=', 'curriculum_module_courses.course_id')
                ->where('curriculum_module_courses.curriculum_module_id', $request->id)
                ->orderBy('curriculum_module_courses.order', 'asc')
                ->dataTable($request);
            CurriculumModuleCourseDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function store(CurriculumModuleCourseStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $item = CurriculumModuleCourse::create($data);
            return ApiResponse::success($item, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    //update
    public function update(CurriculumModuleCourseUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            $item = CurriculumModuleCourse::find($request->id);
            $item->update($data);
            return ApiResponse::success($request->all(), 'Registro actualizado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    //destroy
    public function destroy(Request $request)
    {
        try {
            $item = CurriculumModuleCourse::find($request->id);
            $item->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getItemById(Request $request)
    {
        try {
            $item = CurriculumModuleCourse::find($request->id);

            $item =  CurriculumModuleCourseItemResource::make($item);
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
