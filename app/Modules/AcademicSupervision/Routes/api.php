<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AcademicSupervision\Http\Controllers\AcademicSupervisionController;

// AcademicSupervision
Route::prefix('api/academic-supervision')->group(function () {
    Route::post('load-data-table', [AcademicSupervisionController::class, 'loadDataTable']);
    Route::post('save', [AcademicSupervisionController::class, 'save']);
    Route::post('delete', [AcademicSupervisionController::class, 'destroy']);
    Route::get('active-groups', [AcademicSupervisionController::class, 'getActiveGroups']);
});
