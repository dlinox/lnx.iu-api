<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Teacher\Http\Controllers\TeacherController;

Route::prefix('api/teacher')->group(function () {
    Route::post('load-data-table', [TeacherController::class, 'loadDataTable']);
    Route::post('', [TeacherController::class, 'store']);
    Route::put('', [TeacherController::class, 'update']);
    Route::delete('', [TeacherController::class, 'destroy']);
    
    Route::post('create-user', [TeacherController::class, 'createUser']);
    Route::get('item/by-id/{id}', [TeacherController::class, 'getById']);
    Route::get('items/for-select', [TeacherController::class, 'getItemsForSelect']);
});
