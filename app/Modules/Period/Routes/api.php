<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Period\Http\Controllers\PeriodController;

Route::prefix('api/period')->group(function () {
    Route::post('load-data-table', [PeriodController::class, 'loadDataTable']);
    Route::post('', [PeriodController::class, 'store']);
    Route::put('', [PeriodController::class, 'update']);
    Route::delete('', [PeriodController::class, 'destroy']);

    Route::get('items/for-select', [PeriodController::class, 'getItemsForSelect']);
});
