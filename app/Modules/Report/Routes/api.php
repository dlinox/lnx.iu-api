<?php

use App\Modules\Report\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/reports')->group(function () {
    Route::prefix('student')->group(function () {
        Route::post('enrollments', [ReportController::class, 'studentEnrollements']);
        Route::post('grades', [ReportController::class, 'studentGrades']);
    });

    Route::prefix('group')->group(function () {
        Route::post('enrolled-students', [ReportController::class, 'groupEnrolledStudents']);
        Route::post('enabled-groups', [ReportController::class, 'enabledGroups']);
    });

    Route::prefix('dashboard')->group(function () {
        Route::get('enrollments', [ReportController::class, 'dashboardEnrollments']);
        Route::get('all-students', [ReportController::class, 'dashboardAllStudents']);
        Route::get('all-teachers', [ReportController::class, 'dashboardAllTeachers']);
        Route::get('academic', [ReportController::class, 'dashboardAcademic']);
        Route::get('enrollments-by-year/{year}', [ReportController::class, 'getEnrollmentsByYear']);
        Route::get('enabled-groups-by-year/{year}', [ReportController::class, 'getEnabledGroupsByYear']);
    });

    Route::get('all-years-for-select', [ReportController::class, 'allYearsForSelect']);
});
