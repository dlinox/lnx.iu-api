<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Http\Requests\SignInRequest;
use App\Modules\Auth\Services\AuthenticateService;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use App\Modules\Auth\Services\ResetPasswordService;

class AuthController extends Controller
{
    protected $authenticateService;
    protected $resetPasswordService;

    public function __construct(AuthenticateService $authenticateService, ResetPasswordService $resetPasswordService)
    {
        $this->authenticateService = $authenticateService;
        $this->resetPasswordService = $resetPasswordService;
    }

    public function signIn(SignInRequest $request)
    {
        try {
            $user =  $this->authenticateService->signIn($request);
            return ApiResponse::success($user);
        } catch (\App\Exceptions\AuthException $e) {
            return ApiResponse::error('Error de autenticación', $e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al iniciar sesión');
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $this->resetPasswordService->resetPassword($request);
            return ApiResponse::success('Correo enviado');
        } catch (\App\Exceptions\AuthException $e) {
            return ApiResponse::error('Error de autenticación', $e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al restablecer la contraseña');
        }
    }

    public function user(Request $request)
    {
        try {
            $user = $this->authenticateService->currentUser($request);
            return ApiResponse::success($user);
        } catch (\App\Exceptions\AuthException $e) {
            return ApiResponse::error('Error de autenticación', $e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            return ApiResponse::error('', 'Error al obtener el usuario actual');
        }
    }

    public function signOut(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::success('Sesión cerrada, hasta luego');
    }
}
