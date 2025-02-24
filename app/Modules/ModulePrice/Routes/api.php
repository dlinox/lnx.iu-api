<?php

use Illuminate\Support\Facades\Route;

use App\Modules\ModulePrice\Http\Controllers\ModulePriceController;

Route::prefix('api/module-price')->group(function () {
    Route::post('load-data-table', [ModulePriceController::class, 'loadDataTable']);
    Route::post('', [ModulePriceController::class, 'store']);
    Route::put('', [ModulePriceController::class, 'update']);
    Route::delete('', [ModulePriceController::class, 'destroy']);
});
