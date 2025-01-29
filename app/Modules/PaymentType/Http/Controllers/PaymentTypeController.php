<?php

namespace App\Modules\PaymentType\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\PaymentType\Http\Requests\PaymentTypeStoreRequest;
use App\Modules\PaymentType\Http\Requests\PaymentTypeUpdateRequest;
use App\Modules\PaymentType\Models\PaymentType;
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

    public function store(PaymentTypeStoreRequest $request)
    {
        try {
            $data =  $request->validated();
            $documentType = PaymentType::create($data);
            return ApiResponse::success($documentType, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
    
    public function update(PaymentTypeUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            $documentType = PaymentType::find($request->id);
            $documentType->update($data);
            return ApiResponse::success($request->all(), 'Registro actualizado correctamente', 200);
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
