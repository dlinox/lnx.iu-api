<?php

namespace App\Modules\Teacher\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\Person\Models\Person;
use App\Modules\Teacher\Models\Teacher;
use App\Modules\Teacher\Http\Requests\TeacherStoreRequest;
use App\Modules\Teacher\Http\Requests\TeacherUpdateRequest;

use App\Modules\Teacher\Http\Resources\TeacherDataTableItemsResource;
use App\Modules\Teacher\Http\Resources\TeacherItemResource;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    public function loadDataTable(Request $request)
    {

        try {
            $items = Teacher::select(
                'teachers.id',
                'people.code',
                'people.id as person_id',
                'document_types.name as document_type',
                'people.document_number',
                'people.name',
                'people.last_name_father',
                'people.last_name_mother',
                'people.gender',
                'people.email',
                'people.phone',
                'teachers.is_enabled'
            )
                ->join('people', 'teachers.person_id', '=', 'people.id')
                ->leftJoin('document_types', 'people.document_type_id', '=', 'document_types.id')
                ->orderBy('people.id', 'desc')
                ->dataTable($request);
            TeacherDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function getById(Request $request)
    {
        try {
            $item = Teacher::select(
                'teachers.id',
                'people.code',
                'people.id as person_id',
                'people.document_type_id',
                'people.document_number',
                'people.name',
                'people.last_name_father',
                'people.last_name_mother',
                'people.gender',
                'people.email',
                'people.phone',
                'people.date_of_birth',
                'teachers.is_enabled'
            )
                ->join('people', 'teachers.person_id', '=', 'people.id')
                ->where('teachers.id', $request->id)
                ->first();

            $item =  TeacherItemResource::make($item);
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar el registro');
        }
    }

    public function store(TeacherStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $data =  $request->validated();
            $person = Person::registerItem($data);
            $data['person_id'] = $person->id;
            Teacher::registerItem($data);
            DB::commit();
            return ApiResponse::success(null, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {

            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(TeacherUpdateRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $data['id'] = $request->id;
            Teacher::updateItem($data);
            Person::updateItem($data);
            DB::commit();
            return ApiResponse::success($request->all(), 'Registro actualizado correctamente', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $Teacher = Teacher::find($request->id);
            $Teacher->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
