<?php

use Illuminate\Support\Facades\Route;
use App\Modules\SEssionTime\Http\Controllers\SessionTimeController;

Route::prefix('api/session-time')->group(function () {
    Route::post('load-data-table', [SessionTimeController::class, 'loadDataTable']);
    Route::post('', [SessionTimeController::class, 'store']);
    Route::put('', [SessionTimeController::class, 'update']);
    Route::delete('', [SessionTimeController::class, 'destroy']);
    
    Route::get('items/for-select', [SessionTimeController::class, 'getItemsForSelect']);
});
