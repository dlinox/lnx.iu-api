<?php

use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', function () {

    $pdf = PDF::loadView('pdf.EnrollmentRecord');
    return $pdf->stream();
});
