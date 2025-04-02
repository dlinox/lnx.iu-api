<?php

namespace App\Modules\EnrollmentDeadline\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\EnrollmentDeadline\Http\Requests\EnrollmentDeadlineSaveRequest;
use App\Modules\EnrollmentDeadline\Http\Resources\EnrollmentDeadlineDataTableItemsResource;
use App\Modules\EnrollmentDeadline\Models\EnrollmentDeadline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentDeadlineController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $items = EnrollmentDeadline::join('periods', 'enrollment_deadlines.period_id', '=', 'periods.id')
                ->join('view_month_constants', 'periods.month', '=', 'view_month_constants.value')
                ->select(
                    'enrollment_deadlines.*',
                    DB::raw('CONCAT( periods.year, "-",view_month_constants.label) as period')
                )
                ->orderBy('periods.month', 'desc')
                ->orderBy('enrollment_deadlines.id', 'desc')
                ->dataTable($request);
            EnrollmentDeadlineDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function save(EnrollmentDeadlineSaveRequest $request)
    {
        try {
            $data = $request->validated();
            if ($request->id) {
                EnrollmentDeadline::createExtension($data, $request->id);
            } else {
                $period = EnrollmentDeadline::where('period_id', $data['period_id'])->where('type', 'REGULAR')->exists();
                if ($period) return ApiResponse::error(null, 'Ya existe un periodo de matrícula regular para este periodo');
                EnrollmentDeadline::createRegular($data);
            }
            return ApiResponse::success(null, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    //activeEnrollmentPeriod


    public function getActiveEnrollmentPeriod()
    {
        try {
            $period = EnrollmentDeadline::activeEnrollmentPeriod();
            if (!$period) {
                return ApiResponse::warning(null, 'No existe un periodo de matrícula activo');
            }
            return ApiResponse::success($period);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Ocurrió un error al obtener el periodo de matrícula activo');
        }
    }
}
