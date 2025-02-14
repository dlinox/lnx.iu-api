<?php

namespace App\Modules\Curriculum\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\Curriculum\Http\Requests\CurriculumStoreRequest;
use App\Modules\Curriculum\Http\Requests\CurriculumUpdateRequest;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\Curriculum\Http\Resources\CurriculumDataTableItemsResource;

class CurriculumController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $items = Curriculum::dataTable($request);
            CurriculumDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function store(CurriculumStoreRequest $request)
    {
        try {
            $data =  $request->validated();
            $item = Curriculum::create($data);
            return ApiResponse::success($item, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(CurriculumUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            $item = Curriculum::find($request->id);
            $item->update($data);
            return ApiResponse::success($request->all(), 'Registro actualizado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $item = Curriculum::find($request->id);
            $item->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getDataItem(Request $request)
    {
        try {
            $item = Curriculum::find($request->id);
            $item = CurriculumDataTableItemsResource::make($item);
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
    
    public function getItemsForSelect(Request $request)
    {
        try {
            $item = Curriculum::select('id as value', 'name as label')->enabled()->get();
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
