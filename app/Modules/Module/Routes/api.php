<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Module\Http\Controllers\ModuleController;

Route::prefix('api/module')->middleware('auth:sanctum')->group(function () {
    Route::post('load-data-table', [ModuleController::class, 'loadDataTable']);
    Route::post('', [ModuleController::class, 'store']);
    Route::put('', [ModuleController::class, 'update']);
    Route::delete('', [ModuleController::class, 'destroy']);

    Route::get('items/for-select', [ModuleController::class, 'getItemsForSelect']);
    Route::get('items/for-select/curriculum/{id}', [ModuleController::class, 'getItemsCurriculumForSelect']);
});
