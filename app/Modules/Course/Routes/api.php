<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Course\Http\Controllers\CourseController;

Route::prefix('api/course')->group(function () {
    Route::post('load-data-table', [CourseController::class, 'loadDataTable']);
    Route::post('', [CourseController::class, 'store']);
    Route::put('', [CourseController::class, 'update']);
    Route::delete('', [CourseController::class, 'destroy']);
    
    Route::get('items/for-select', [CourseController::class, 'getItemsForSelect']);
    Route::get('prerequisite-by-curriculum/items/for-select/{module}', [CourseController::class, 'getPreRequisiteByCurriculumItemsForSelect']);
});
