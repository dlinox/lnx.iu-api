<?php

namespace App\Modules\Student\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;

use App\Mail\SendCredentialsMail;

use App\Modules\PreRegister\Models\PreRegister;
use App\Modules\Student\Models\Student;
use App\Modules\User\Models\User;

use App\Modules\Student\Http\Requests\StudentStoreRequest;
use App\Modules\Student\Http\Requests\StudentUpdateRequest;

use App\Modules\Student\Http\Resources\StudentDataTableItemsResource;
use App\Modules\Student\Http\Resources\StudentItemResource;

class StudentController extends Controller
{
    public function loadDataTable(Request $request)
    {

        try {
            $items = Student::select(
                'students.id',
                'students.code',
                'document_types.name as document_type',
                'students.document_number',
                'students.name',
                'students.last_name_father',
                'students.last_name_mother',
                'genders.short_name as gender',
                'students.email',
                'students.phone',
                'student_types.name as student_type',
                'students.is_enabled',
                'users.id as user_id',
            )
                ->leftJoin('document_types', 'students.document_type_id', '=', 'document_types.id')
                ->leftJoin('student_types', 'students.student_type_id', '=', 'student_types.id')
                ->leftJoin('genders', 'students.gender_id', 'genders.id')
                ->leftJoin('users', function ($join) {
                    $join->on('users.model_id', '=', 'students.id')
                        ->where('users.model_type', '=', 'student');
                })
                ->orderBy('students.id', 'desc')
                ->dataTable($request);
            StudentDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function loadForm(Request $request)
    {
        try {
            $item = Student::select(
                'students.id',
                'students.code',
                'students.document_type_id',
                'students.document_number',
                'students.name',
                'students.last_name_father',
                'students.last_name_mother',
                'students.gender_id as gender',
                'students.email',
                'students.phone',
                'students.date_of_birth',
                'students.student_type_id',
                'students.is_enabled'
            )
                ->where('students.id', $request->id)
                ->first();

            $item = StudentItemResource::make($item);
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
            $email = Student::where('email', $data['email'])->exists();
            if ($email) return ApiResponse::error(null, 'Ya existe un estudiante con este correo', 422);
            $student = Student::registerItem($data);
            $user = User::createAccountStudent($student);
            Mail::to($student->email)->send(new SendCredentialsMail($user));
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
            $item = Student::find($request->id);
            $enrollment = $item->enrollments()->exists();
            $enrollmentGroup = $item->enrollmentGroups()->exists();
            if ($enrollment || $enrollmentGroup) {
                return ApiResponse::error(null, 'El estudiante tiene matriculas, no se puede eliminar', 422);
            }
            $preRegister = PreRegister::where('email', $item->email)->first();
            $user = User::where('model_id', $item->id)->where('model_type', 'student')->first();
            DB::beginTransaction();
            if ($user)  $user->delete();
            if ($preRegister)  $preRegister->delete();
            $item->delete();
            DB::commit();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }

    public function createUser(Request $request)
    {

        $student = Student::find($request->id);
        if (!$student) return ApiResponse::error(null, 'Datos del estudiante no encontrados, recargue la pagina y vuelva a intentar', 422);

        if (!filter_var($student->email, FILTER_VALIDATE_EMAIL)) return ApiResponse::error(null, 'El estudiante no tiene un correo electrónico válido, no se puede crear la cuenta', 422);

        if (!$student->document_number) return ApiResponse::error(null, 'El estudiante no tiene un número de documento, no se puede crear la cuenta', 422);

        $user = User::where('email', $student->email)->where('model_type', 'student')->where('model_id', '!=', $request->id)->exists();

        if ($user) return ApiResponse::error(null, 'Ya existe una cuenta con este correo electronico', 422);

        $hasUser = User::where('model_id', $request->id)->where('model_type', 'student')->exists();
        if ($hasUser) return ApiResponse::error(null, 'Ya existe una cuenta para este estudiante', 422);

        try {
            DB::beginTransaction();
            $student = Student::find($request->id);
            $user = User::createAccountStudent($student);
            Mail::to($student->email)->send(new SendCredentialsMail($user));
            DB::commit();
            return ApiResponse::success(null, 'Usuario creado correctamente', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }
    
    public function getItemsForSelect(Request $request)
    {
        try {

            $query = Student::select(
                'students.id as value',
                DB::raw("CONCAT_WS(' ',students.name,students.last_name_father,students.last_name_mother) as label")
            )
                ->enabled();

            if ($request->search) {
                $query->where(function ($query) use ($request) {
                    $query->whereRaw("CONCAT_WS(' ',students.name,students.last_name_father,students.last_name_mother) like '%" . $request->search . "%'");
                });
            }
            if ($request->limit) {
                $query->limit($request->limit);
            } else {
                $query->limit(10);
            }
            if ($request->id) {
                $query->whereIn('students.id', explode(',', $request->id));
            }
            $item = $query->get();
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function searchList(Request $request)
    {
        try {
            $items = Student::select(
                'students.id',
                'students.code',
                'student_types.name as studentType',
                'document_types.name as documentType',
                'students.document_number as documentNumber',
                'students.name',
                DB::raw("CONCAT_WS(' ', students.last_name_father, students.last_name_mother) as lastName"),
                'students.is_enabled as isEnabled'
            )
                ->leftJoin('document_types', 'students.document_type_id', '=', 'document_types.id')
                ->join('student_types', 'students.student_type_id', '=', 'student_types.id')
                ->when($request->name, function ($query) use ($request) {
                    $query->where('students.name', 'like', '%' . $request->name . '%');
                })
                ->when($request->lastNameFather, function ($query) use ($request) {
                    $query->where('students.last_name_father', 'like', '%' . $request->lastNameFather . '%');
                })
                ->when($request->lastNameMother, function ($query) use ($request) {
                    $query->where('students.last_name_mother', 'like', '%' . $request->lastNameMother . '%');
                })
                ->when($request->documentNumber, function ($query) use ($request) {
                    $query->where('students.document_number', 'like', '%' . $request->documentNumber . '%');
                })
                ->when($request->code, function ($query) use ($request) {
                    $query->where('students.code', 'like', '%' . $request->code . '%');
                })
                ->limit(10)
                ->get();

            return ApiResponse::success($items);
        } catch (\Exception $e) {

            return ApiResponse::error($e->getMessage());
        }
    }

    public function getInfoById(Request $request)
    {
        try {
            $item = Student::getInfoById($request->id);
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
