<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Area\Http\Controllers\AreaController;

Route::prefix('api/area')->group(function () {
    Route::post('load-data-table', [AreaController::class, 'loadDataTable']);
    Route::post('', [AreaController::class, 'store']);
    Route::put('', [AreaController::class, 'update']);
    Route::delete('', [AreaController::class, 'destroy']);
    
    Route::get('items/for-select', [AreaController::class, 'getItemsForSelect']);
});
