<?php

use Illuminate\Support\Facades\Route;
use App\Modules\CurriculumCourse\Http\Controllers\CurriculumCourseController;

Route::prefix('api/curriculum/course')->group(function () {
    Route::post('load-data-table', [CurriculumCourseController::class, 'loadDataTable']);
    Route::post('', [CurriculumCourseController::class, 'store']);
    Route::put('', [CurriculumCourseController::class, 'update']);
    Route::delete('', [CurriculumCourseController::class, 'destroy']);
    Route::get('get-item-by-id/{id}', [CurriculumCourseController::class, 'getItemById']);
});
