<?php

namespace App\Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\User\Services\ProfileService;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function changePassword(Request $request)
    {
        try {
            $this->profileService->changePassword($request);
            return ApiResponse::success(true, 'Contraseña cambiada correctamente', 200);
        } catch (\App\Exceptions\ApiException $e) {
            return ApiResponse::error($e->getMessage(), $e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cambiar la contraseña', 500);
        }
    }
}
