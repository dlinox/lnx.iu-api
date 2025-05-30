<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Course\Http\Controllers\CourseController;

Route::prefix('api/course')->group(function () {
    Route::post('load-data-table', [CourseController::class, 'loadDataTable']);
    Route::post('', [CourseController::class, 'store']);
    Route::put('', [CourseController::class, 'update']);
    Route::delete('', [CourseController::class, 'destroy']);

    Route::get('items/for-select', [CourseController::class, 'getItemsForSelect']);
    Route::get('get-item-by-id/{id}', [CourseController::class, 'getItemById']);

    Route::get('items/for-select/module/{moduleId}', [CourseController::class, 'getItemsByModuleForSelect']);
    
    //deprecated
    Route::get('prerequisite-by-curriculum/items/for-select/{curriculumId}', [CourseController::class, 'getPreRequisiteByCurriculumItemsForSelect']);
    //deprecated
    Route::get('items/for-select/curriculum/{id}', [CourseController::class, 'getItemsCurriculumForSelect']);
    // deprecated
    Route::get('curriculum/{curriculumId}/module/{moduleId}', [CourseController::class, 'getCurriculumCourses']);
});
