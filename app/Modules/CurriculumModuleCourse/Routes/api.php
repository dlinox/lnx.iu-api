<?php

use Illuminate\Support\Facades\Route;
use App\Modules\CurriculumModuleCourse\Http\Controllers\CurriculumModuleCourseController;

Route::prefix('api/curriculum/module/course')->group(function () {
    Route::post('load-data-table', [CurriculumModuleCourseController::class, 'loadDataTable']);
    Route::post('', [CurriculumModuleCourseController::class, 'store']);
    Route::put('', [CurriculumModuleCourseController::class, 'update']);
    Route::delete('', [CurriculumModuleCourseController::class, 'destroy']);

    //getItemById
    Route::get('get-item-by-id/{id}', [CurriculumModuleCourseController::class, 'getItemById']);

});