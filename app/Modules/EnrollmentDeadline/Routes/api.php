<?php

use Illuminate\Support\Facades\Route;
use App\Modules\EnrollmentDeadline\Http\Controllers\EnrollmentDeadlineController;

Route::prefix('api/enrollment-deadlines')->group(function () {
    Route::post('load-data-table', [EnrollmentDeadlineController::class, 'loadDataTable']);
    Route::post('save', [EnrollmentDeadlineController::class, 'save']);
});
