<?php

namespace App\Modules\CoursePrice\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\CoursePrice\Http\Requests\CoursePriceStoreRequest;
use App\Modules\CoursePrice\Models\CoursePrice;
use App\Modules\CoursePrice\Http\Resources\CoursePriceDataTableItemsResource;
use App\Modules\Price\Http\Requests\PriceUpdateRequest as RequestsPriceUpdateRequest;

class CoursePriceController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {

            $items = CoursePrice::select(
                'course_prices.id',
                'course_prices.course_id',
                'courses.name as course',
                'course_prices.student_type_id',
                'student_types.name as student_type',
                'course_prices.presential_price',
                'course_prices.virtual_price',
                'course_prices.is_enabled',
                'course_prices.curriculum_id'
            )
                ->join('courses', 'courses.id', '=', 'course_prices.course_id')
                ->join('student_types', 'student_types.id', '=', 'course_prices.student_type_id')
                ->where('course_prices.curriculum_id', $request->filters['curriculumId'])
                ->orderBy('courses.name')
                ->orderBy('student_types.name')
                ->dataTable($request);
            CoursePriceDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }


    public function store(CoursePriceStoreRequest $request)
    {
        try {
            $data =  $request->validated();
            $documentType = CoursePrice::create($data);
            return ApiResponse::success($documentType, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(RequestsPriceUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            $documentType = CoursePrice::find($request->id);
            $documentType->update($data);
            return ApiResponse::success($request->all(), 'Registro actualizado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }


    public function destroy(Request $request)
    {
        try {
            $item = CoursePrice::find($request->id);
            $item->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
