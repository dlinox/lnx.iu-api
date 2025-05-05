<?php

use App\Modules\User\Http\Controllers\ProfileController;
use App\Modules\User\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/user')->group(function () {
    Route::post('load-data-table', [UserController::class, 'loadDataTable']);
    Route::post('', [UserController::class, 'store']);
    Route::post('account', [UserController::class, 'storeAccount']);
    Route::put('', [UserController::class, 'update']);
    Route::put('account', [UserController::class, 'updateAccount']);
    Route::delete('', [UserController::class, 'destroy']);
    Route::get('items/for-select', [UserController::class, 'getItemsForSelect']);
});


Route::prefix('api/user/profile')->middleware('auth:sanctum')->group(function () {
    Route::post('change-password', [ProfileController::class, 'changePassword']);
});
