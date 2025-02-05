<?php

namespace App\Modules\Schedule\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\Schedule\Http\Requests\ScheduleStoreRequest;
use App\Modules\Schedule\Http\Requests\ScheduleUpdateRequest;
use App\Modules\Schedule\Models\Schedule;
use App\Modules\Schedule\Http\Resources\ScheduleDataTableItemsResource;

class ScheduleController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $items = Schedule::dataTable($request);
            ScheduleDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function store(ScheduleStoreRequest $request)
    {
        try {
            $data =  $request->validated();
            $item = Schedule::create($data);
            return ApiResponse::success($item, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(ScheduleUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            $item = Schedule::find($request->id);
            $item->update($data);
            return ApiResponse::success($request->all(), 'Registro actualizado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $item = Schedule::find($request->id);
            $item->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getItemsForSelect(Request $request)
    {
        try {
            $item = Schedule::select('id as value', 'name as label')->enabled()->get();
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
