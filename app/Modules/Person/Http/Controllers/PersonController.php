<?php

namespace App\Modules\Person\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\Person\Http\Requests\PersonStoreRequest;
use App\Modules\Person\Http\Requests\PersonUpdateRequest;
use App\Modules\Person\Models\Person;
use App\Modules\Person\Http\Resources\PersonDataTableItemsResource;

class PersonController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $Persons = Person::dataTable($request);
            PersonDataTableItemsResource::collection($Persons);
            return ApiResponse::success($Persons);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function store(PersonStoreRequest $request)
    {
        try {
            $data =  $request->validated();
            $Person = Person::create($data);
            return ApiResponse::success($Person, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(PersonUpdateRequest $request)
    {
        try {
            $data = $request->validated();
            $Person = Person::find($request->id);
            $Person->update($data);
            return ApiResponse::success($request->all(), 'Registro actualizado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
    
    public function destroy(Request $request)
    {
        try {
            $Person = Person::find($request->id);
            $Person->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
