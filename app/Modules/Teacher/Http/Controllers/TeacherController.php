<?php

namespace App\Modules\Teacher\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Mail\SendCredentialsMail;
use App\Modules\Group\Models\Group;
use App\Modules\Teacher\Models\Teacher;
use App\Modules\Teacher\Http\Requests\TeacherStoreRequest;
use App\Modules\Teacher\Http\Requests\TeacherUpdateRequest;

use App\Modules\Teacher\Http\Resources\TeacherDataTableItemsResource;
use App\Modules\Teacher\Http\Resources\TeacherItemResource;
use App\Modules\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TeacherController extends Controller
{
    public function loadDataTable(Request $request)
    {

        try {
            $items = Teacher::select(
                'teachers.id',
                'teachers.code',
                'document_types.name as document_type',
                'teachers.document_number',
                'teachers.name',
                'teachers.last_name_father',
                'teachers.last_name_mother',
                'teachers.gender_id as gender',
                'teachers.email',
                'teachers.phone',
                'users.id as user_id',
                'teachers.is_enabled'
            )
                ->leftJoin('document_types', 'teachers.document_type_id', '=', 'document_types.id')
                ->leftJoin('users', function ($join) {
                    $join->on('users.model_id', '=', 'teachers.id')
                        ->where('users.model_type', '=', 'teacher');
                })
                ->orderBy('teachers.id', 'desc')
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
                'teachers.code',
                'teachers.document_type_id',
                'teachers.document_number',
                'teachers.name',
                'teachers.last_name_father',
                'teachers.last_name_mother',
                'teachers.gender_id as gender',
                'teachers.email',
                'teachers.phone',
                'teachers.date_of_birth',
                'teachers.is_enabled'
            )
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
            $teacher = Teacher::registerItem($data);
            $user = User::createAccountTeacher($teacher);
            Mail::to($teacher->email)->send(new SendCredentialsMail($user));
            DB::commit();
            return ApiResponse::success(null, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage(), 'Error al crear el registro');
        }
    }

    public function update(TeacherUpdateRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $data['id'] = $request->id;
            Teacher::updateItem($data);
            DB::commit();
            return ApiResponse::success($request->all(), 'Registro actualizado correctamente', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }

    public function createUser(Request $request)
    {

        $teacher = Teacher::find($request->id);
        if (!$teacher) return ApiResponse::error(null, 'Datos del docente no encontrados', 404);

        if (!filter_var($teacher->email, FILTER_VALIDATE_EMAIL)) return ApiResponse::error(null, 'El docente no tiene un correo electrónico válido, no se puede crear la cuenta', 422);

        if (!$teacher->document_number) return ApiResponse::error(null, 'El docente no tiene un número de documento, no se puede crear la cuenta', 422);

        $user = User::where('email', $teacher->email)->where('model_type', 'teacher')->where('model_id', '!=', $request->id)->exists();

        if ($user) return ApiResponse::error(null, 'Ya existe una cuenta con este correo electronico', 422);

        $hasUser = User::where('model_id', $request->id)->where('model_type', 'student')->exists();
        if ($hasUser) return ApiResponse::error(null, 'Ya existe una cuenta para este docente', 422);

        try {
            DB::beginTransaction();
            $user = User::createAccountTeacher($teacher);
            Mail::to($teacher->email)->send(new SendCredentialsMail($user));
            DB::commit();
            return ApiResponse::success(null, 'Usuario creado correctamente', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            $teacher = Teacher::find($request->id);

            if (Group::where('teacher_id', $request->id)->exists()) {
                return ApiResponse::error(null, 'No se puede eliminar el docente porque tiene grupos asignados', 422);
            }

            $user = User::where('model_id', $request->id)->where('model_type', 'teacher')->first();
            if ($user) $user->delete();
            $teacher->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }


    public function getItemsForSelect(Request $request)
    {
        try {

            $query = Teacher::select(
                'teachers.id as value',
                DB::raw("CONCAT_WS(' ',teachers.name,teachers.last_name_father,teachers.last_name_mother) as label")
            )->enabled();

            if ($request->search) {
                $query->where(function ($query) use ($request) {
                    $query->whereRaw("CONCAT_WS(' ',teachers.name,teachers.last_name_father,teachers.last_name_mother) like '%" . $request->search . "%'");
                });
            }
            if ($request->limit) {
                $query->limit($request->limit);
            } else {
                $query->limit(10);
            }
            if ($request->id) {
                $query->whereIn('teachers.id', explode(',', $request->id));
            }
            $item = $query->get();
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
