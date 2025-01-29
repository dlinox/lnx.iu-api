<?php

use Illuminate\Support\Facades\Route;
use App\Modules\StudentType\Http\Controllers\StudentTypeController;

Route::prefix('api/student-type')->group(function () {
    Route::post('load-data-table', [StudentTypeController::class, 'loadDataTable']);
    Route::post('', [StudentTypeController::class, 'store']);
    Route::put('', [StudentTypeController::class, 'update']);
    Route::delete('', [StudentTypeController::class, 'destroy']);

    Route::get('items/for-select', [StudentTypeController::class, 'getItemsForSelect']);
});
