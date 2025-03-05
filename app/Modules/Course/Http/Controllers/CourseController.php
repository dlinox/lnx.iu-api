<?php

namespace App\Modules\Course\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\Course\Http\Requests\CourseStoreRequest;
use App\Modules\Course\Http\Requests\CourseUpdateRequest;
use App\Modules\Course\Models\Course;
use App\Modules\Course\Http\Resources\CourseDataTableItemsResource;
use App\Modules\Course\Http\Resources\CourseItemResource;
// use App\Modules\CurriculumModule\Models\CurriculumModule;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $items = Course::select(
                'courses.id',
                'courses.code',
                'courses.name',
                'courses.is_enabled',
                'courses.area_id',
                'areas.name as area',
                'modules.name as module',
                'curriculums.name as curriculum',
                'courses.hours_practice',
                'courses.hours_theory',
                'courses.credits',
                'courses.pre_requisite_id',
                'courses.order',
                'courses.description',
                'courses.units',
                DB::raw("CONCAT_WS(' - ', pre_requisite.code, pre_requisite.name) as pre_requisite")
            )
                ->join('areas', 'courses.area_id', '=', 'areas.id')
                ->join('curriculums', 'curriculums.id', '=', 'courses.curriculum_id')
                ->join('modules', 'modules.id', '=', 'courses.module_id')
                ->leftJoin('courses as pre_requisite', 'pre_requisite.id', '=', 'courses.pre_requisite_id')
                ->where('courses.curriculum_id', 'LIKE', '%' . $request->filters['curriculumId'] . '%')
                ->dataTable($request);
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
            $item = Course::select(
                'id as value',
                DB::raw("CONCAT_WS(' - ', code, name) as label")
            )
                ->distinct()
                ->when($request->has('curriculumId'), function ($query) use ($request) {
                    return $query->where('curriculum_id', $request->curriculumId);
                })
                ->enabled()
                ->orderBy('label')
                ->get();
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getItemById(Request $request)
    {
        try {
            $item = Course::find($request->id);
            $item =  CourseItemResource::make($item);
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

    //geCurriculumCourses
    public function getCurriculumCourses(Request $request)
    {
        try {
            $courses = Course::geCurriculumCourses($request->curriculumId, $request->moduleId);
            return ApiResponse::success($courses);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    //getItemsForSelect
    public function getItemsCurriculumForSelect(Request $request)
    {
        try {
            $item = Course::getItemsForSelect($request->id);
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
