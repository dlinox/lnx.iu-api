<?php

use Illuminate\Support\Facades\Route;
use App\Modules\GradeDeadline\Http\Controllers\GradeDeadlineController;

Route::prefix('api/grade-deadlines')->group(function () {
    Route::post('load-data-table', [GradeDeadlineController::class, 'loadDataTable']);
    Route::post('save', [GradeDeadlineController::class, 'save']);
    Route::get('active-enrollment-period', [GradeDeadlineController::class, 'getActiveEnrollmentPeriod']);
});
