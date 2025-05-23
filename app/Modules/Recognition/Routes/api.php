<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Recognition\Http\Controllers\RecognitionController;

Route::prefix('api/recognition')->group(function () {

    Route::post('load-data-table', [RecognitionController::class, 'loadDataTable']);

    Route::post('', [RecognitionController::class, 'store']);
    Route::delete('', [RecognitionController::class, 'destroy']);

    Route::get('module-enrollments/items/for-select/{studentId}', [RecognitionController::class, 'getModuleEnrollmentsForSelect']);
    Route::get('course-extracurriculars-by-student/items/for-select/{studentId}', [RecognitionController::class, 'getExtracurricularsForSelect']);

    Route::post('courses-by-module/items/for-select', [RecognitionController::class, 'getCourseByModuleForSelect']);
});
