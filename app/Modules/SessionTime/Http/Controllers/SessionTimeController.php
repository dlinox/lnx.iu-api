<?php

namespace App\Modules\SessionTime\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\SessionTime\Http\Requests\SessionTimeStoreRequest;
use App\Modules\SessionTime\Http\Requests\SessionTimeUpdateRequest;
use App\Modules\SessionTime\Models\SessionTime;
use App\Modules\SessionTime\Http\Resources\SessionTimeDataTableItemsResource;

class SessionTimeController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $items = SessionTime::dataTable($request);
            SessionTimeDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function store(SessionTimeStoreRequest $request)
    {
        try {
            $data =  $request->validated();
            $item = SessionTime::create($data);
            return ApiResponse::success($item, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(SessionTimeUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            $item = SessionTime::find($request->id);
            $item->update($data);
            return ApiResponse::success($request->all(), 'Registro actualizado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $item = SessionTime::find($request->id);
            $item->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getItemsForSelect(Request $request)
    {
        try {
            $item = SessionTime::select('id as value', 'name as label')->enabled()->get();
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
