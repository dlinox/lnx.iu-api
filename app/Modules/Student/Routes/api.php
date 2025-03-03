<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Student\Http\Controllers\StudentController;

Route::prefix('api/student')->group(function () {
    Route::post('load-data-table', [StudentController::class, 'loadDataTable']);
    Route::post('', [StudentController::class, 'store']);
    Route::put('', [StudentController::class, 'update']);
    Route::delete('', [StudentController::class, 'destroy']);

    Route::get('item/by-id/{id}', [StudentController::class, 'getById']);
    Route::post('search/list', [StudentController::class, 'searchList']);
    //getItemsForSelect
    Route::get('items/for-select', [StudentController::class, 'getItemsForSelect']);
});
