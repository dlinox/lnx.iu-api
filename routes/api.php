<?php

use App\Modules\Auth\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('/auth')->group(function () {
    Route::post('/sign-in', [AuthController::class, 'signIn']);
    //signInStudent
    Route::post('/sign-in-student', [AuthController::class, 'signInStudent']);
    Route::post('/sign-up', [AuthController::class, 'signUp']);

    Route::post('/sign-out', [AuthController::class, 'signOut'])->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
});
