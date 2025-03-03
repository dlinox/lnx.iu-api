<?php

use App\Modules\User\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/user')->group(function () {
    Route::post('load-data-table', [UserController::class, 'loadDataTable']);
    Route::post('', [UserController::class, 'store']);
    Route::put('', [UserController::class, 'update']);
    Route::delete('', [UserController::class, 'destroy']);

    Route::get('items/for-select', [UserController::class, 'getItemsForSelect']);
});
