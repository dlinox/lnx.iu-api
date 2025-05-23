<?php

use App\Modules\Enrollment\Http\Controllers\EnrollmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/enrollment')->middleware('auth:sanctum')->group(function () {
    Route::post('load-data-table', [EnrollmentController::class, 'loadDataTable']);
    Route::post('student-enrollment-avaliable', [EnrollmentController::class, 'getStudentEnrollmentAvaliable']);
    Route::post('{studentId}/student/{curriculumId}/curriculum', [EnrollmentController::class, 'getModulesEnrollment']);
    Route::post('validate-payment', [EnrollmentController::class, 'validatePaymentEnrollment']);
    Route::post('module-store', [EnrollmentController::class, 'enrollmentModuleStore']);
    Route::post('enabled-groups', [EnrollmentController::class, 'enabledGroups']);
    Route::post('group-store', [EnrollmentController::class, 'enrollmentGroupStore']);
    Route::post('group-update', [EnrollmentController::class, 'enrollmentGroupUpdate']);
    Route::post('group-reserved', [EnrollmentController::class, 'enrollmentGroupReserved']);
    Route::post('group-cancel', [EnrollmentController::class, 'enrollmentGroupCancel']);
    Route::post('get-enrollment-group-payments', [EnrollmentController::class, 'getEnrollmentGroupPayments']);
    Route::post('student-enrollment-avaliable-special', [EnrollmentController::class, 'getStudentEnrollmentAvaliableSpacial']);
    Route::post('download-enrollment-pdf', [EnrollmentController::class, 'downloadEnrollmentPDF']);
});
