<?php

use Illuminate\Support\Facades\Route;
use App\Modules\CurriculumModule\Http\Controllers\CurriculumModuleController;

Route::prefix('api/curriculum/module')->group(function () {
    Route::post('load-data-table', [CurriculumModuleController::class, 'loadDataTable']);
    Route::post('', [CurriculumModuleController::class, 'store']);
    Route::put('', [CurriculumModuleController::class, 'update']);
    Route::delete('', [CurriculumModuleController::class, 'destroy']);

    Route::get('get-data-item/{id}', [CurriculumModuleController::class, 'getDataItem']);

    Route::get('get-item-by-id/{id}', [CurriculumModuleController::class, 'getItemById']);
});
