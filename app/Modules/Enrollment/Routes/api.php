<?php

use App\Modules\Enrollment\Http\Controllers\EnrollmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/enrollment')->group(function () {
    Route::post('{id}/student-enrollment/{curriculumId}/curriculum', [EnrollmentController::class, 'getStudentEnrollment']);
    Route::post('{studentId}/student/{curriculumId}/curriculum', [EnrollmentController::class, 'getModulesEnrollment']);
    Route::post('validate-payment', [EnrollmentController::class, 'validatePaymentEnrollment']);
    //enrollmentModuleStore
    Route::post('module-store', [EnrollmentController::class, 'enrollmentModuleStore']);
    //enabledGroups
    Route::post('enabled-groups', [EnrollmentController::class, 'enabledGroups']);
    //enrollmentGroupStore
    Route::post('group-store', [EnrollmentController::class, 'enrollmentGroupStore']);
});
