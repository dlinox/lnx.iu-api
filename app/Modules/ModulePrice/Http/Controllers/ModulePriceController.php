<?php

namespace App\Modules\ModulePrice\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\ModulePrice\Http\Resources\ModulePriceDataTableItemsResource;
use App\Modules\Price\Http\Requests\PriceStoreRequest;
use App\Modules\Price\Http\Requests\PriceUpdateRequest;
use App\Modules\Price\Models\Price;
use App\Modules\ModulePrice\Models\ModulePrice;

class ModulePriceController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {

            $items = ModulePrice::select(
                'module_prices.id',
                'module_prices.module_id',
                'modules.name as module',
                'module_prices.student_type_id',
                'student_types.name as student_type',
                'module_prices.price',
                'module_prices.is_enabled',
                'module_prices.curriculum_id'
            )
                ->join('modules', 'modules.id', '=', 'module_prices.module_id')
                ->join('student_types', 'student_types.id', '=', 'module_prices.student_type_id')
                ->where('module_prices.curriculum_id', $request->filters['curriculumId'])
                ->orderBy('modules.name')
                ->orderBy('student_types.name')
                ->dataTable($request);
            ModulePriceDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }


    public function store(PriceStoreRequest $request)
    {
        try {
            $data =  $request->validated();
            $documentType = Price::create($data);
            return ApiResponse::success($documentType, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(PriceUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            $documentType = Price::find($request->id);
            $documentType->update($data);
            return ApiResponse::success($request->all(), 'Registro actualizado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }


    public function destroy(Request $request)
    {
        try {
            $item = Price::find($request->id);
            $item->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
