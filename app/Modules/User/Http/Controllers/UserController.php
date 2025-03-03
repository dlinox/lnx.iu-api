<?php

namespace App\Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\User\Http\Requests\UserStoreRequest;
use App\Modules\User\Http\Requests\UserUpdateRequest;
use App\Modules\User\Models\User;
use App\Modules\User\Http\Resources\UserDataTableItemsResource;
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
                ->where('users.account_level', $request->filters['level'])
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
            $data['account_level'] = $request->level;
            $item = User::create($data);
            $item->syncRoles($request->role);
            DB::commit();
            return ApiResponse::success($item, 'Registro creado correctamente', 201);
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
                ->where('Users.account_level', $request->level)
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
                ->where('permissions.account_level', $request->level)
                ->get();
            return ApiResponse::success($item);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
