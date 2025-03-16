<?php

namespace App\Modules\StudentType\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\StudentType\Http\Requests\SaveRequest;
use App\Modules\StudentType\Models\StudentType;
use App\Modules\StudentType\Http\Resources\StudentTypeDataTableItemsResource;

class StudentTypeController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $documentTypes = StudentType::dataTable($request);
            StudentTypeDataTableItemsResource::collection($documentTypes);
            return ApiResponse::success($documentTypes);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function save(SaveRequest $request)
    {
        try {
            $data =  $request->validated();
            StudentType::updateOrCreate(['id' => $request->id], $data);
            return ApiResponse::success(null, $request->id ? 'Registro actualizado correctamente' : 'Registro creado correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $documentType = StudentType::find($request->id);
            $documentType->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getItemsForSelect()
    {
        try {
            $documentTypes = StudentType::select('id as value', 'name as label')->enabled()->get();
            return ApiResponse::success($documentTypes);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
