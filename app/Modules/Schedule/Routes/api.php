<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Schedule\Http\Controllers\ScheduleController;

Route::prefix('api/schedule')->group(function () {
    Route::post('load-data-table', [ScheduleController::class, 'loadDataTable']);
    Route::post('', [ScheduleController::class, 'store']);
    Route::put('', [ScheduleController::class, 'update']);
    Route::delete('', [ScheduleController::class, 'destroy']);
    
    Route::get('items/for-select', [ScheduleController::class, 'getItemsForSelect']);
});
