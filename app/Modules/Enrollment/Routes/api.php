<?php

use App\Modules\Enrollment\Http\Controllers\EnrollmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/enrollment')->middleware('auth:sanctum')->group(function () {
    Route::post('student-enrollment-avaliable', [EnrollmentController::class, 'getStudentEnrollmentAvaliable']);
    Route::post('{studentId}/student/{curriculumId}/curriculum', [EnrollmentController::class, 'getModulesEnrollment']);
    Route::post('validate-payment', [EnrollmentController::class, 'validatePaymentEnrollment']);
    //enrollmentModuleStore
    Route::post('module-store', [EnrollmentController::class, 'enrollmentModuleStore']);
    //enabledGroups
    Route::post('enabled-groups', [EnrollmentController::class, 'enabledGroups']);
    //enrollmentGroupStore
    Route::post('group-store', [EnrollmentController::class, 'enrollmentGroupStore']);

    //downloadEnrollmentRecord
    // Route::post('download-enrollment-record', [EnrollmentController::class, 'downloadEnrollmentRecord']);

    //STUDENT
    //storeStudentEnrollment
    Route::post('store-student-enrollment', [EnrollmentController::class, 'storeStudentEnrollment']);
    //storeGroupEnrollment
    Route::post('store-group-enrollment', [EnrollmentController::class, 'storeGroupEnrollment']);
    //enabledGroupsEnrollment
    Route::post('enabled-groups-enrollment', [EnrollmentController::class, 'enabledGroupsEnrollment']);
});
