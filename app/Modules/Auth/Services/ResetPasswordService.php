<?php

namespace App\Modules\Auth\Services;

use App\Mail\ResetPasswordMail;
use App\Modules\Auth\Support\AuthSupport;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Support\Facades\Mail;

class  ResetPasswordService
{
    protected $authService;
    protected $userRepository;
    protected $authSupport;
    public function __construct(AuthService $authService, UserRepository $userRepository, AuthSupport $authSupport)
    {
        $this->userRepository = $userRepository;
        $this->authService = $authService;
        $this->authSupport = $authSupport;
    }

    public function resetPassword($request)
    {
        $user = $this->userRepository->findByEmail($request->email);
        if (!$user) return throw new \App\Exceptions\AuthException('Usuario no encontrado', 404);
        if ($user->is_enabled == 0) return throw new \App\Exceptions\AuthException('Usuario inactivo, no se puede restablecer la contraseña', 403);
        $password = $this->authSupport->generatePassword();
        $this->userRepository->updatePassword($user, $password);
        Mail::to($user->email)->send(new ResetPasswordMail(['password' => $password,]));
        return $user;
    }
}
