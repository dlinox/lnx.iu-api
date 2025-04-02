<?php

namespace App\Modules\Auth\Repositories;

use App\Modules\User\Models\User;

class AuthRepository
{

    public static function getUserRole(User $user)
    {
        return $user->roles()->first();
    }

    public static function getUserPermissions(User $user)
    {
        return $user->permissions()->pluck('name')->toArray() ?? [];
    }

    public static function getUserToken(User $user)
    {
        $token = request()->bearerToken();
        if ($token) return $token;
        return $user->createToken('admin-access-token')->plainTextToken;
    }
}
