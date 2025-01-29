<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Curriculum\Http\Controllers\CurriculumController;

Route::prefix('api/curriculum')->group(function () {
    Route::post('load-data-table', [CurriculumController::class, 'loadDataTable']);
    Route::post('', [CurriculumController::class, 'store']);
    Route::put('', [CurriculumController::class, 'update']);
    Route::delete('', [CurriculumController::class, 'destroy']);

    Route::get('get-data-item/{id}', [CurriculumController::class, 'getDataItem']);
    
    Route::get('items/for-select', [CurriculumController::class, 'getItemsForSelect']);
});
