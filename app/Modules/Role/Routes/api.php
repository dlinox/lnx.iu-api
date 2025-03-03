<?php

use App\Modules\Role\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/role')->group(function () {
    Route::post('load-data-table', [RoleController::class, 'loadDataTable']);
    Route::post('', [RoleController::class, 'store']);
    Route::put('', [RoleController::class, 'update']);
    Route::delete('', [RoleController::class, 'destroy']);

    Route::get('items/for-select', [RoleController::class, 'getItemsForSelect']);
});


