<?php

use App\Modules\EnrollmentGroup\Http\Controllers\EnrollmentGroupController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/enrollment-groups')->group(function () {

    // loadDataTable
    Route::post('load-data-table', [EnrollmentGroupController::class, 'loadDataTable']);
    //changeStatusGroup
    Route::post('change-status-group', [EnrollmentGroupController::class, 'changeStatusGroup']);
});
