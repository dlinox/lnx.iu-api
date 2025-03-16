<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Laboratory\Http\Controllers\LaboratoryController;

Route::prefix('api/laboratory')->group(function () {
    Route::post('load-data-table', [LaboratoryController::class, 'loadDataTable']);
    Route::post('save', [LaboratoryController::class, 'save']);
    Route::delete('', [LaboratoryController::class, 'destroy']);

    Route::get('items/for-select', [LaboratoryController::class, 'getItemsForSelect']);
});
