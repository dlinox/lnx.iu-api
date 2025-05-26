<?php

namespace App\Modules\Auth\Repositories;

use App\Modules\User\Models\User;
use Illuminate\Support\Facades\DB;

class AuthRepository
{

    public static function getUserRole(User $user)
    {
        return $user->roles()->first();
    }

    public static function getUserPermissions(User $user)
    {
        $permissions = DB::table('model_has_roles')
            ->select('permissions.name')
            ->join('role_has_permissions', 'model_has_roles.role_id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->where('model_has_roles.model_id', $user->id)
            ->get();
        return $permissions->pluck('name')->toArray();
    }

    public static function getUserToken(User $user)
    {
        $token = request()->bearerToken();
        if ($token) return $token;
        return $user->createToken('admin-access-token')->plainTextToken;
    }
}
