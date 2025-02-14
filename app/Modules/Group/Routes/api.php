<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Group\Http\Controllers\GroupController;

Route::prefix('api/group')->group(function () {
    Route::post('load-data-table', [GroupController::class, 'loadDataTable']);
    Route::post('save', [GroupController::class, 'save']);
    Route::delete('', [GroupController::class, 'destroy']);
    Route::post('load-form', [GroupController::class, 'loadForm']);
});
