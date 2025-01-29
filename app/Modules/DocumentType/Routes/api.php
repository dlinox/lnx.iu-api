<?php

use Illuminate\Support\Facades\Route;
use App\Modules\DocumentType\Http\Controllers\DocumentTypeController;

Route::prefix('api/document-type')->group(function () {
    Route::post('load-data-table', [DocumentTypeController::class, 'loadDataTable']);
    Route::post('', [DocumentTypeController::class, 'store']);
    Route::put('', [DocumentTypeController::class, 'update']);
    Route::delete('', [DocumentTypeController::class, 'destroy']);
    
    Route::get('items/for-select', [DocumentTypeController::class, 'getItemsForSelect']);
});
