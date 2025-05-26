<?php

namespace App\Modules\Role\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\Role\Http\Requests\RoleStoreRequest;
use App\Modules\Role\Http\Requests\RoleUpdateRequest;
use App\Modules\Role\Models\Role;
use App\Modules\Role\Http\Resources\RoleDataTableItemsResource;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as SpatieRole;

class RoleController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $items = Role::select(
                'roles.id',
                'roles.name',
                'roles.is_enabled',
            )
                ->where('roles.model_type', $request->filters['level'])
                ->dataTable($request);
            RoleDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function store(RoleStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $data =  $request->validated();
            $data['guard_name'] = 'sanctum';
            $data['model_type'] = $request->level;
            $item = Role::create($data);
            $role = SpatieRole::find($item->id);
            $role->syncPermissions($request->permissions);
            DB::commit();
            return ApiResponse::success($item, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }

    public function update(RoleUpdateRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $item = Role::find($request->id);
            $role = SpatieRole::find($request->id);
            $role->syncPermissions($request->permissions);
            $item->update($data);
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
            $role = SpatieRole::find($request->id);
            if ($role->users->count() > 0) {
                return ApiResponse::error(null, 'No se puede eliminar el rol, ya que esta asignado a un usuario');
            }
            $item = Role::find($request->id);
            $item->delete();
            return ApiResponse::success(null, 'Registro eliminado correctamente', 204);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    public function getItemsForSelect(Request $request)
    {
        try {
            $item = Role::select(
                'roles.id as value',
                'roles.name as label'
            )
                ->where('roles.is_enabled', true)
                ->where('roles.model_type', $request->level)
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
                'permissions.name as id',
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

    public function  getAllPermissions()
    {
        try {
            $permissions = Permission::select(
                'permissions.id',
                'permissions.display_name',
                'permissions.name',
            )
                ->where('permissions.group', null)
                ->get()->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'display_name' => $permission->display_name,
                        'children' => Permission::where('group', $permission->name)
                            ->select('id', 'display_name', 'name')
                            ->get()
                    ];
                });


            return ApiResponse::success($permissions);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
    // asignPermissions
    public function asignPermissions(Request $request)
    {
        try {
            DB::beginTransaction();
            $role = SpatieRole::find($request->id);
            if (!$role) {
                return ApiResponse::error(null, 'El rol no existe', 404);
            }
            $role->syncPermissions($request->permissions);
            DB::commit();
            return ApiResponse::success(null, 'Permisos asignados correctamente', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage());
        }
    }
}
