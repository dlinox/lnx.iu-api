<?php

namespace App\Modules\ModulePrice\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\ModulePrice\Http\Requests\ModulePriceStoreRequest;
use App\Modules\ModulePrice\Http\Requests\ModulePriceUpdateRequest;
use App\Modules\ModulePrice\Http\Resources\ModulePriceDataTableItemsResource;

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
                'modules.curriculum_id'
            )
                ->join('modules', 'modules.id', '=', 'module_prices.module_id')
                ->join('student_types', 'student_types.id', '=', 'module_prices.student_type_id')
                ->where('modules.curriculum_id', $request->filters['curriculumId'])
                ->orderBy('modules.name')
                ->orderBy('student_types.name')
                ->dataTable($request);
            ModulePriceDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }


    public function store(ModulePriceStoreRequest $request)
    {
        try {
            $data =  $request->validated();
            $item = ModulePrice::create($data);
            return ApiResponse::success($item, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(ModulePriceUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            $item = ModulePrice::find($request->id);
            $item->update($data);
            return ApiResponse::success($request->all(), 'Registro actualizado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }


    public function destroy(Request $request)
    {
        try {
            $item = ModulePrice::find($request->id);
            $item->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
