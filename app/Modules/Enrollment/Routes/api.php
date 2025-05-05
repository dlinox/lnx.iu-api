<?php

use App\Modules\Enrollment\Http\Controllers\EnrollmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/enrollment')->middleware('auth:sanctum')->group(function () {

    //loadDataTable
    Route::post('load-data-table', [EnrollmentController::class, 'loadDataTable']);

    Route::post('student-enrollment-avaliable', [EnrollmentController::class, 'getStudentEnrollmentAvaliable']);
    Route::post('{studentId}/student/{curriculumId}/curriculum', [EnrollmentController::class, 'getModulesEnrollment']);
    Route::post('validate-payment', [EnrollmentController::class, 'validatePaymentEnrollment']);
    //enrollmentModuleStore
    Route::post('module-store', [EnrollmentController::class, 'enrollmentModuleStore']);
    //enabledGroups
    Route::post('enabled-groups', [EnrollmentController::class, 'enabledGroups']);
    //enrollmentGroupStore
    Route::post('group-store', [EnrollmentController::class, 'enrollmentGroupStore']);
    //enrollmentGroupUpdate
    Route::post('group-update', [EnrollmentController::class, 'enrollmentGroupUpdate']);
    //enrollmentGroupReserved
    Route::post('group-reserved', [EnrollmentController::class, 'enrollmentGroupReserved']);
    //enrollmentGroupCancel
    Route::post('group-cancel', [EnrollmentController::class, 'enrollmentGroupCancel']);
    //getEnrollmentGroupPayments
    Route::post('get-enrollment-group-payments', [EnrollmentController::class, 'getEnrollmentGroupPayments']);

    // //`/enrollment/student-enrollment-avaliable-special`, getStudentEnrollmentAvaliableSpacial
    Route::post('student-enrollment-avaliable-special', [EnrollmentController::class, 'getStudentEnrollmentAvaliableSpacial']);



    //downloadEnrollmentPDF
    Route::post('download-enrollment-pdf', [EnrollmentController::class, 'downloadEnrollmentPDF']);

    //DEPRECATED
    //STUDENT
    // //storeStudentEnrollment
    // Route::post('store-student-enrollment', [EnrollmentController::class, 'storeStudentEnrollment']);
    // //storeGroupEnrollment
    // Route::post('store-group-enrollment', [EnrollmentController::class, 'storeGroupEnrollment']);
    // //enabledGroupsEnrollment
    // Route::post('enabled-groups-enrollment', [EnrollmentController::class, 'enabledGroupsEnrollment']);
});
