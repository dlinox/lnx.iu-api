<?php

namespace App\Modules\User\Services;

use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class ProfileService
{
    
    protected $userRepository;
    
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function changePassword($request)
    {
        $user = Auth::user();
        if (!$user) return throw new \App\Exceptions\AuthException('Usuario no encontrado', 404);
        if (!password_verify($request->password, $user->password)) return throw new \App\Exceptions\ApiException('Contraseña incorrecta', 401);
        if ($request->newPassword == $request->password) return throw new \App\Exceptions\ApiException('La nueva contraseña no puede ser igual a la actual', 401);
        if ($request->newPassword != $request->confirmPassword) return throw new \App\Exceptions\ApiException('Las contraseñas no coinciden', 401);
        if (strlen($request->newPassword) < 8) return throw new \App\Exceptions\ApiException('La nueva contraseña debe tener al menos 6 caracteres', 401);
        $this->userRepository->updatePassword($user, $request->newPassword);
        return $user;
    }
}
