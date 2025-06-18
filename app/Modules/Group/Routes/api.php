<?php

use Illuminate\Support\Facades\Route;

use App\Modules\Group\Http\Controllers\GroupController;

Route::prefix('api/group')->group(function () {
    Route::post('load-data-table', [GroupController::class, 'loadDataTable']);
    Route::post('save', [GroupController::class, 'save']);
    //clonar grupos de un periodo a otro
    Route::post('clone', [GroupController::class, 'clone']);
    //reservar matriculas de un periodo a otro
    Route::post('reservations', [GroupController::class, 'reservations']);

    Route::delete('', [GroupController::class, 'destroy']);
    Route::post('load-form', [GroupController::class, 'loadForm']);
    // group/options/teacher
    Route::get('options/teacher', [GroupController::class, 'getTeachers']);
    // group/options/laboratory
    Route::get('options/laboratory', [GroupController::class, 'getLaboratories']);
    //group/save-status
    Route::post('save-status', [GroupController::class, 'saveStatus']);

    Route::post('get-by-module-and-period', [GroupController::class, 'getByModuleAndPeriod']);
});
