<?php

namespace App\Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\Student\Models\Student;
use App\Modules\Teacher\Models\Teacher;
use App\Modules\User\Http\Requests\UserStoreRequest;
use App\Modules\User\Http\Requests\UserUpdateRequest;
use App\Modules\User\Models\User;
use App\Modules\User\Http\Resources\UserDataTableItemsResource;
use App\Modules\User\Services\ProfileService;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $items = User::select(
                'users.id',
                'users.name',
                'users.username',
                'users.email',
                'users.is_enabled',
                'users.model_id',
            )
                ->where('users.model_type', $request->filters['level'])
                ->dataTable($request);
            UserDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function store(UserStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $data =  $request->validated();
            $data['guard_name'] = 'sanctum';
            $data['model_id'] = $request->modelId ?? null;
            $data['model_type'] = $request->level;
            $item = User::create($data);
            $item->syncRoles($request->role);
            DB::commit();
            return ApiResponse::success($item, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }

    public function storeAccount(Request $request)
    {
        try {

            if ($request->level == 'student') {
                $item = Student::find($request->modelId);
                if (!$item) return ApiResponse::error(null, 'El estudiante no existe', 404);
                if (!$item->document_number) return ApiResponse::error(null, 'El estudiante no tiene un numero de documento', 422);
                if (!filter_var($item->email, FILTER_VALIDATE_EMAIL)) return ApiResponse::error(null, 'El estudiante no tiene un correo electronico valido', 422);
            }
            if ($request->level == 'teacher') {
                $item = Teacher::find($request->modelId);
                if (!$item) return ApiResponse::error(null, 'El docente no existe', 404);
                if (!$item->document_number) return ApiResponse::error(null, 'El docente no tiene un numero de documento', 422);
                if (!filter_var($item->email, FILTER_VALIDATE_EMAIL)) return ApiResponse::error(null, 'El docente no tiene un correo electronico valido', 422);
            }

            $account = User::where('model_id', $request->modelId)
                ->where('model_type', $request->level)
                ->exists();

            if ($account) return ApiResponse::error(null, "Ya existe una cuenta asociada a este registro", 422);

            DB::beginTransaction();
            $data = [];
            $data['guard_name'] = 'sanctum';
            $data['model_id'] = $request->modelId;
            $data['model_type'] = $request->level;
            $data['name'] = $item->name . ' ' . $item->last_name_father . ' ' . $item->last_name_mother;
            $data['username'] = $item->document_number;
            $data['email'] = $item->email;
            $data['is_enabled'] = true;
            $data['password'] = $request->password;

            $item = User::create($data);
            $item->syncRoles($request->role);

            DB::commit();
            return ApiResponse::success($item, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }

    public function updateAccount(Request $request)
    {
        try {
            $user = User::where('model_id', $request->modelId)
                ->where('model_type', $request->level)
                ->first();
            if (!$user) return ApiResponse::error(null, 'El registro no existe', 404);
            DB::beginTransaction();
            $data = [];
            $data['is_enabled'] = $request->isEnabled;
            if ($request->password != null && $request->password != '') {
                $data['password'] = $request->password;
            }
            $user->update($data);
            DB::commit();
            return ApiResponse::success($data, 'Registro actualizado correctamente', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(UserUpdateRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $item = User::find($request->id);
            $item->update($data);
            $item->syncRoles($request->role);
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
            $item = User::find($request->id);
            $item->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getItemsForSelect(Request $request)
    {
        try {
            $item = User::select(
                'Users.id as value',
                'Users.name as label'
            )
                ->where('Users.is_enabled', true)
                ->where('Users.model_type', $request->level)
                ->get();

            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
    //permisos getItemsForSelect
    public function getPermissionsForSelect(Request $request)
    {
        try {
            $item = Permission::select(
                'permissions.id as value',
                'permissions.name as label'
            )
                ->where('permissions.is_enabled', true)
                ->where('permissions.model_type', $request->level)
                ->get();
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
