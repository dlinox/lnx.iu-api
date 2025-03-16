<?php

use Illuminate\Support\Facades\Route;
use App\Modules\StudentType\Http\Controllers\StudentTypeController;

Route::prefix('api/student-type')->group(function () {
    Route::post('load-data-table', [StudentTypeController::class, 'loadDataTable']);
    Route::post('save', [StudentTypeController::class, 'save']);
    Route::delete('', [StudentTypeController::class, 'destroy']);
    Route::get('items/for-select', [StudentTypeController::class, 'getItemsForSelect']);
});
