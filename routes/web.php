<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Hello World';
});

///mpdf demo
Route::get('/mpdf', function () {
    $mpdf = new \Mpdf\Mpdf(
        [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 32,
            'margin_bottom' => 10,
            'margin_header' => 5,
            'margin_footer' => 5,
        ]
    );

    $htmlContent = view('pdf.EnrolledStudents.index')->render();
    $htmlHeader = view('pdf.EnrolledStudents._header')->render();
    $htmlFooter = view('pdf.EnrolledStudents._footer')->render();

    $mpdf->SetHTMLHeader($htmlHeader);
    $mpdf->SetHTMLFooter($htmlFooter);
    $mpdf->SetTitle('Acta de Notas');

    $mpdf->WriteHTML($htmlContent);

    return response($mpdf->Output('', 'S'), 200)
        ->header('Content-Type', 'application/pdf');
});


Route::get('/grades', function () {
    $mpdf = new \Mpdf\Mpdf(
        [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 32,
            'margin_bottom' => 10,
            'margin_header' => 5,
            'margin_footer' => 5,
        ]
    );

    $htmlContent = view('pdf.Report.Student.Grades')->render();
    $htmlHeader = view('pdf.Report.Student._header')->render();
    $htmlFooter = view('pdf.Report.Student._footer')->render();

    $mpdf->SetHTMLHeader($htmlHeader);
    $mpdf->SetHTMLFooter($htmlFooter);
    $mpdf->SetTitle('Acta de Notas');

    $mpdf->WriteHTML($htmlContent);

    return response($mpdf->Output('', 'S'), 200)
        ->header('Content-Type', 'application/pdf');
});


//attendance
Route::get('/attendances', function () {
    $mpdf = new \Mpdf\Mpdf(
        [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 32,
            'margin_bottom' => 10,
            'margin_header' => 5,
            'margin_footer' => 5,
        ]
    );

    $htmlContent = view('pdf.Report.Student.Attendances')->render();
    $htmlHeader = view('pdf.Report.Student._header')->render();
    $htmlFooter = view('pdf.Report.Student._footer')->render();

    $mpdf->SetHTMLHeader($htmlHeader);
    $mpdf->SetHTMLFooter($htmlFooter);
    $mpdf->SetTitle('Acta de Notas');

    $mpdf->WriteHTML($htmlContent);

    return response($mpdf->Output('', 'S'), 200)
        ->header('Content-Type', 'application/pdf');
});

//enrollments

Route::get('/enrollments', function () {
    $mpdf = new \Mpdf\Mpdf(
        [
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 32,
            'margin_bottom' => 10,
            'margin_header' => 5,
            'margin_footer' => 5,
        ]
    );

    $htmlContent = view('pdf.Report.Student.Enrollments')->render();
    $htmlHeader = view('pdf.Report.Student._header')->render();
    $htmlFooter = view('pdf.Report.Student._footer')->render();

    $mpdf->SetHTMLHeader($htmlHeader);
    $mpdf->SetHTMLFooter($htmlFooter);
    $mpdf->SetTitle('Acta de Notas');

    $mpdf->WriteHTML($htmlContent);

    return response($mpdf->Output('', 'S'), 200)
        ->header('Content-Type', 'application/pdf');
});
