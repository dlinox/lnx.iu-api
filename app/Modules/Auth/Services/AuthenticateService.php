<?php

namespace App\Modules\Auth\Services;

use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class  AuthenticateService
{
    protected $authService;
    protected $userRepository;

    public function __construct(AuthService $authService, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->authService = $authService;
    }

    public function signIn($request)
    {
        $user = $this->userRepository->findByUsernameAndType($request->username, 'admin');
        if (!$user || !Hash::check($request->password, $user->password)) return throw new \App\Exceptions\AuthException('Credenciales incorrectas', 401);
        if ($user->is_enabled == 0) return throw new \App\Exceptions\AuthException('Usuario inactivo', 401);
        return $this->authService->authPayload($user);
    }

    public function currentUser($request)
    {
        try {
            $user = $request->user();
            return $this->authService->authPayload($user);
        } catch (\Exception $e) {
            return throw new \App\Exceptions\AuthException('Error al obtener el usuario actual', 500);
        }
    }
}
