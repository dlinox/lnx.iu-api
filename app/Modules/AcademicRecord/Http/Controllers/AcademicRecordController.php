<?php

namespace App\Modules\AcademicRecord\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\AcademicRecord\Http\Resources\AcademicRecordDataTableItemsResource;
use App\Modules\AcademicRecord\Models\AcademicRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicRecordController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {

            $items = AcademicRecord::selectRaw('
                    groups.name as `group`,
                    courses.name as course,
                    CONCAT(teachers.name, " ", teachers.last_name_father, " ", teachers.last_name_mother) as teacher,
                    CONCAT(periods.year, " ", months.name) as period,
                    GROUP_CONCAT(DISTINCT academic_records.code SEPARATOR ",") as recordCodes,
                    MAX(academic_records.created_at) as last_created_at,
                    JSON_ARRAYAGG(
                        JSON_OBJECT(
                            "id", academic_records.id,
                            "observations", academic_records.observations,
                            "createdAt", academic_records.created_at,
                            "code", academic_records.code,
                            "isEnabled", academic_records.is_enabled
                        )
                    ) as records_json
                ')
                ->join('groups', 'academic_records.group_id', '=', 'groups.id')
                ->join('courses', 'groups.course_id', '=', 'courses.id')
                ->join('periods', 'groups.period_id', '=', 'periods.id')
                ->join('months', 'periods.month', '=', 'months.id')
                ->join('teachers', 'groups.teacher_id', '=', 'teachers.id')
                ->groupBy('academic_records.group_id')
                ->dataTable($request);
            AcademicRecordDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }


    public function getRecordPdf(Request $request)
    {
        try {
            $academicRecord = AcademicRecord::find($request->id);

            if (!$academicRecord) {
                $errorPdf = $this->generateErrorPdf('No se encontró el registro.');
                return response($errorPdf->Output('', 'S'), 200)
                    ->header('Content-Type', 'application/pdf');
            }

            $dataPDF = json_decode($academicRecord->payload);

            $pdf = $this->generateRecordPdf($dataPDF, true);

            return response($pdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al generar el acta de notas');
        }
    }

    // printRecordPdf
    public function printRecordPdf(Request $request)
    {
        try {
            $academicRecord = AcademicRecord::find($request->id);

            if (!$academicRecord) {
                $errorPdf = $this->generateErrorPdf('No se encontró el registro.');
                return response($errorPdf->Output('', 'S'), 200)
                    ->header('Content-Type', 'application/pdf');
            }

            $dataPDF = json_decode($academicRecord->payload);

            if (!$request->code) {
                return ApiResponse::error('El código es requerido', 'Error al generar el acta de notas');
            }

            //verificar si el código ya existe
            $existingRecord = AcademicRecord::where('code', $request->code)
                ->where('group_id', "!=", $academicRecord->group_id)
                ->exists();
            if ($existingRecord) {
                return ApiResponse::error('El código ya existe', 'Error al generar el acta de notas');
            }

            DB::beginTransaction();



            AcademicRecord::where('group_id', $academicRecord->group_id)
                ->where('id', '!=', $academicRecord->id)
                ->where('created_at', '<', $academicRecord->created_at)
                ->update([
                    'is_enabled' => false,
                    'code' => null
                ]);

            $academicRecord->code = $request->code;
            $academicRecord->is_enabled = true;
            $academicRecord->save();

            $dataPDF->code = $request->code;

            $pdf = $this->generateRecordPdf($dataPDF, false);

            DB::commit();
            return response($pdf->Output('', 'S'), 200)
                ->header('Content-Type', 'application/pdf');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage(), 'Error al generar el acta de notas');
        }
    }


    public function generateRecordPdf($data, $watermark = false)
    {

        $group = $data->group;
        $students = $data->students;
        $userInitials = $data->userInitials;
        $code = $data->code ?? null;

        $mpdf = new \Mpdf\Mpdf(
            [
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 20,
                'margin_right' => 10,
                'margin_top' => 40,
                'margin_bottom' => 10,
                'margin_header' => 5,
                'margin_footer' => 5,
                'showWatermarkText' => $watermark,

            ]
        );

        $htmlContent =  view('pdf.AcademicRecord.index', compact('students', 'group', 'code'))->render();
        $htmlHeader =  view('pdf.AcademicRecord._header')->render();
        $htmlFooter =  view('pdf.AcademicRecord._footer', compact('userInitials'))->render();

        $mpdf->SetWatermarkText('VISTA PREVIA',  0.1);
        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->SetTitle('Acta de Notas [' . $group->name . ' - ' . $group->year . ' ' . $group->month . ']');

        $mpdf->WriteHTML($htmlContent);

        return $mpdf;
    }

    public function generateErrorPdf($message)
    {
        $mpdf = new \Mpdf\Mpdf(
            [
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 10,
                'margin_bottom' => 10,
                'margin_header' => 5,
                'margin_footer' => 5,
            ]
        );
        $mpdf->SetTitle('Error al generar el PDF');
        $mpdf->WriteHTML('<h1>Error</h1><p>' . $message . '</p>');
        return $mpdf;
    }
}
