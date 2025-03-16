<?php

use Illuminate\Support\Facades\Route;
use App\Modules\DocumentType\Http\Controllers\DocumentTypeController;

Route::prefix('api/document-type')->middleware('auth:sanctum')
    ->group(function () {
        Route::post('load-data-table', [DocumentTypeController::class, 'loadDataTable']);
        Route::post('save', [DocumentTypeController::class, 'save']);
        Route::delete('', [DocumentTypeController::class, 'destroy']);
        Route::get('items/for-select', [DocumentTypeController::class, 'getItemsForSelect']);
    });
