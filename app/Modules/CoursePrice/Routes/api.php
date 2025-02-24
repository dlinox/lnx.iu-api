<?php

use Illuminate\Support\Facades\Route;

use App\Modules\CoursePrice\Http\Controllers\CoursePriceController;

Route::prefix('api/course-price')->group(function () {
    Route::post('load-data-table', [CoursePriceController::class, 'loadDataTable']);
    Route::post('', [CoursePriceController::class, 'store']);
    Route::put('', [CoursePriceController::class, 'update']);
    Route::delete('', [CoursePriceController::class, 'destroy']);
});
