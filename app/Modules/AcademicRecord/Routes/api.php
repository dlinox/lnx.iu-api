<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AcademicRecord\Http\Controllers\AcademicRecordController;

Route::prefix('api/academic-records')->group(function () {
    Route::post('load-data-table', [AcademicRecordController::class, 'loadDataTable']);
    Route::post('get-record-pdf/{id}', [AcademicRecordController::class, 'getRecordPdf']);
});
