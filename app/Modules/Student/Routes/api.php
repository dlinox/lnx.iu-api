<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Student\Http\Controllers\StudentController;

Route::prefix('api/student')->group(function () {
    Route::post('load-data-table', [StudentController::class, 'loadDataTable']);
    Route::post('', [StudentController::class, 'store']);
    Route::put('', [StudentController::class, 'update']);
    Route::delete('', [StudentController::class, 'destroy']);

    Route::get('item/load-form/{id}', [StudentController::class, 'loadForm']);

    Route::post('search/list', [StudentController::class, 'searchList']);

    Route::get('items/for-select', [StudentController::class, 'getItemsForSelect']);

    //getInfoById
    Route::get('info/{id}', [StudentController::class, 'getInfoById']);
});
