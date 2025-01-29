<?php

use Illuminate\Support\Facades\Route;
use App\Modules\PaymentType\Http\Controllers\PaymentTypeController;

Route::prefix('api/payment-type')->group(function () {
    Route::post('load-data-table', [PaymentTypeController::class, 'loadDataTable']);
    Route::post('', [PaymentTypeController::class, 'store']);
    Route::put('', [PaymentTypeController::class, 'update']);
    Route::delete('', [PaymentTypeController::class, 'destroy']);
});
