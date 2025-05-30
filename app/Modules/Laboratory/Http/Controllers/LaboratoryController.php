<?php

namespace App\Modules\Laboratory\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\Group\Models\Group;
use App\Modules\Laboratory\Models\Laboratory;
use App\Modules\Laboratory\Http\Requests\LaboratorySaveRequest;
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

    public function save(LaboratorySaveRequest $request)
    {
        try {
            $data =  $request->validated();
            Laboratory::updateOrCreate(['id' => $request->id], $data);
            return ApiResponse::success(null, $request->id ? 'Registro actualizado correctamente' : 'Registro creado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $item = Laboratory::find($request->id);

            if (!$item) return ApiResponse::error(null, 'No se encontró el laboratorio', 404);
            if (Group::where('laboratory_id', $item->id)->exists()) {
                return ApiResponse::error(null, 'No se puede eliminar el laboratorio tiene grupos asociados', 422);
            }
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
