<?php

namespace App\Modules\Laboratory\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\Laboratory\Http\Requests\LaboratoryStoreRequest;
use App\Modules\Laboratory\Http\Requests\LaboratoryUpdateRequest;
use App\Modules\Laboratory\Models\Laboratory;
use App\Modules\Laboratory\Http\Resources\LaboratoryDataTableItemsResource;

class LaboratoryController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $items = Laboratory::dataTable($request);
            LaboratoryDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function store(LaboratoryStoreRequest $request)
    {
        try {
            $data =  $request->validated();
            $item = Laboratory::create($data);
            return ApiResponse::success($item, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(LaboratoryUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            $item = Laboratory::find($request->id);
            $item->update($data);
            return ApiResponse::success($request->all(), 'Registro actualizado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $item = Laboratory::find($request->id);
            $item->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getItemsForSelect(Request $request)
    {
        try {
            $item = Laboratory::select('id as value', 'name as label')->enabled()->get();
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
