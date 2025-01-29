<?php

namespace App\Modules\Student\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\Person\Models\Person;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Http\Requests\StudentStoreRequest;
use App\Modules\Student\Http\Requests\StudentUpdateRequest;

use App\Modules\Student\Http\Resources\StudentDataTableItemsResource;
use App\Modules\Student\Http\Resources\StudentItemResource;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function loadDataTable(Request $request)
    {

        try {
            $items = Student::select(
                'students.id',
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
                'student_types.name as student_type',
                'students.is_enabled'
            )
                ->join('people', 'students.person_id', '=', 'people.id')
                ->leftJoin('document_types', 'people.document_type_id', '=', 'document_types.id')
                ->leftJoin('student_types', 'students.student_type_id', '=', 'student_types.id')
                ->orderBy('people.id', 'desc')
                ->dataTable($request);
            StudentDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function getById(Request $request)
    {
        try {
            $item = Student::select(
                'students.id',
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
                'students.student_type_id',
                'students.is_enabled'
            )
                ->join('people', 'students.person_id', '=', 'people.id')
                ->where('students.id', $request->id)
                ->first();

            $item =  StudentItemResource::make($item);
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar el registro');
        }
    }

    public function store(StudentStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $data =  $request->validated();
            $person = Person::registerItem($data);
            $data['person_id'] = $person->id;
            Student::registerItem($data);
            DB::commit();
            return ApiResponse::success(null, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {

            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(StudentUpdateRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $data['id'] = $request->id;
            Student::updateItem($data);
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
            $Student = Student::find($request->id);
            $Student->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
