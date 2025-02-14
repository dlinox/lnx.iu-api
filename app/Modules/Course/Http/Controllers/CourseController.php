<?php

namespace App\Modules\Course\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\Course\Http\Requests\CourseStoreRequest;
use App\Modules\Course\Http\Requests\CourseUpdateRequest;
use App\Modules\Course\Models\Course;
use App\Modules\Course\Http\Resources\CourseDataTableItemsResource;
// use App\Modules\CurriculumModule\Models\CurriculumModule;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $items = Course::dataTable($request);
            CourseDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function store(CourseStoreRequest $request)
    {
        try {
            $data =  $request->validated();
            $item = Course::create($data);
            return ApiResponse::success($item, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(CourseUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            $item = Course::find($request->id);
            $item->update($data);
            return ApiResponse::success($request->all(), 'Registro actualizado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $item = Course::find($request->id);
            $item->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getItemsForSelect(Request $request)
    {
        try {
            $item = Course::select('id as value', 'name as label')->enabled()->get();
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getPreRequisiteByCurriculumItemsForSelect(Request $request)
    {
        try {
            $item = Course::select(
                'curriculum_courses.id as value',
                DB::raw("CONCAT_WS(' - ', curriculum_courses.code, courses.name) as label")
            )
                ->join('curriculum_courses', 'curriculum_courses.course_id', '=', 'courses.id')
                ->where('curriculum_courses.curriculum_id', $request->curriculumId)
                ->where('curriculum_courses.is_enabled', true)
                ->get();
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
