<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Repositories\AuthRepository;
use App\Modules\User\Models\User;

class AuthService
{
    protected $authRepository;

    public function  __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function authPayload(User $user)
    {
        try {
            $role = $this->authRepository->getUserRole($user);
            $token = $this->authRepository->getUserToken($user);
            return [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $role->name,
                    'redirectTo' => $role->redirect_to ?? '/',
                ],
                'token' => $token,
                'permissions' => implode('|', $this->authRepository->getUserPermissions($user)),
            ];
        } catch (\Exception $e) {
            return throw new \App\Exceptions\AuthException('Error al obtener el usuario', 500);
        }
    }
}
