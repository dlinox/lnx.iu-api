<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Price\Http\Controllers\PriceController;

Route::prefix('api/price')->group(function () {
    Route::post('load-data-table', [PriceController::class, 'loadDataTable']);
    Route::post('', [PriceController::class, 'store']);
    Route::put('', [PriceController::class, 'update']);
    Route::delete('', [PriceController::class, 'destroy']);
});
