<?php

namespace App\Modules\Period\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\Period\Http\Requests\PeriodStoreRequest;
use App\Modules\Period\Http\Requests\PeriodUpdateRequest;
use App\Modules\Period\Models\Period;
use App\Modules\Period\Http\Resources\PeriodDataTableItemsResource;
use Illuminate\Support\Facades\DB;

class PeriodController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $items = Period::orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->dataTable($request);
            PeriodDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function store(PeriodStoreRequest $request)
    {
        try {
            $data =  $request->validated();
            $item = Period::create($data);
            return ApiResponse::success($item, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(PeriodUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            $item = Period::find($request->id);
            $item->update($data);
            return ApiResponse::success($request->all(), 'Registro actualizado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $item = Period::find($request->id);
            $item->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getItemsForSelect(Request $request)
    {
        try {
            $item = Period::select(
                'periods.id as value',
                DB::raw('CONCAT(year, "-", view_month_constants.label) as label')
            )->join('view_month_constants', 'periods.month', '=', 'view_month_constants.value')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->whereRaw('CONCAT(year, "-", view_month_constants.label) LIKE ?', ['%' . $request->search . '%'])
                ->limit($request->limit ? $request->limit : 12)
                ->get();

            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getCurrent()
    {
        try {
            $item = Period::select(
                'periods.id as value',
                DB::raw('CONCAT(year, "-", view_month_constants.label) as label')
            )->join('view_month_constants', 'periods.month', '=', 'view_month_constants.value')
                ->where('is_enabled', 1)
                ->first();

            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al obtener el periodo actual');
        }
    }
}
