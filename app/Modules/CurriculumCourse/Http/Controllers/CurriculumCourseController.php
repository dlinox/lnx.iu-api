<?php

namespace App\Modules\CurriculumCourse\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\CurriculumCourse\Http\Requests\CurriculumCourseStoreRequest;
use App\Modules\CurriculumCourse\Http\Requests\CurriculumCourseUpdateRequest;
use App\Modules\CurriculumCourse\Http\Resources\CurriculumCourseDataTableItemsResource;
use App\Modules\CurriculumCourse\Http\Resources\CurriculumCourseItemResource;
use App\Modules\CurriculumCourse\Models\CurriculumCourse;
use Illuminate\Http\Request;

class CurriculumCourseController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $items = CurriculumCourse::select(
                'curriculum_courses.id',
                'curriculum_courses.order',
                'curriculum_courses.code',
                'curriculum_courses.hours_practice',
                'curriculum_courses.hours_theory',
                'curriculum_courses.credits',
                'courses.name as course',
                'areas.name as area',
                'modules.name as module',
                'curriculum_courses.pre_requisite_id',
                'curriculum_courses.is_enabled',
            )->join('courses', 'courses.id', '=', 'curriculum_courses.course_id')
                ->where('curriculum_courses.curriculum_id', $request->filters['curriculumId'])
                ->join('areas', 'areas.id', '=', 'curriculum_courses.area_id')
                ->leftJoin('modules', 'modules.id', '=', 'curriculum_courses.module_id')
                ->orderBy('curriculum_courses.module_id', 'asc')
                ->orderBy('curriculum_courses.order', 'asc')
                ->dataTable($request);
            CurriculumCourseDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function store(CurriculumCourseStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $item = CurriculumCourse::create($data);
            return ApiResponse::success($item, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    //update
    public function update(CurriculumCourseUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            $item = CurriculumCourse::find($request->id);
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
            $item = CurriculumCourse::find($request->id);
            $item->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getItemById(Request $request)
    {
        try {
            $item = CurriculumCourse::find($request->id);

            $item =  CurriculumCourseItemResource::make($item);
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
