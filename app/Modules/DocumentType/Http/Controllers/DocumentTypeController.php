<?php

namespace App\Modules\DocumentType\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\DocumentType\Http\Requests\DocumentTypeStoreRequest;
use App\Modules\DocumentType\Http\Requests\DocumentTypeUpdateRequest;
use App\Modules\DocumentType\Models\DocumentType;
use App\Modules\DocumentType\Http\Resources\DocumentTypeDataTableItemsResource;

class DocumentTypeController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $documentTypes = DocumentType::dataTable($request);
            DocumentTypeDataTableItemsResource::collection($documentTypes);
            return ApiResponse::success($documentTypes);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function store(DocumentTypeStoreRequest $request)
    {
        try {
            $data =  $request->validated();
            $documentType = DocumentType::create($data);
            return ApiResponse::success($documentType, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(DocumentTypeUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            $documentType = DocumentType::find($request->id);
            $documentType->update($data);
            return ApiResponse::success($request->all(), 'Registro actualizado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $documentType = DocumentType::find($request->id);
            $documentType->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getItemsForSelect(Request $request)
    {
        try {
            $documentTypes = DocumentType::select('id as value', 'name as label')->enabled()->get();
            return ApiResponse::success($documentTypes);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
