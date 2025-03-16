<?php

namespace App\Modules\PaymentType\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\PaymentType\Models\PaymentType;
use App\Modules\PaymentType\Http\Requests\SaveRequest;
use App\Modules\PaymentType\Http\Resources\PaymentTypeDataTableItemsResource;

class PaymentTypeController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $documentTypes = PaymentType::dataTable($request);
            PaymentTypeDataTableItemsResource::collection($documentTypes);
            return ApiResponse::success($documentTypes);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function save(SaveRequest $request)
    {
        try {
            $data =  $request->validated();
            PaymentType::updateOrCreate(['id' => $request->id], $data);
            return ApiResponse::success(null, $request->id ? 'Registro actualizado correctamente' : 'Registro creado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $documentType = PaymentType::find($request->id);
            $documentType->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
