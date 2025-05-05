<?php

namespace App\Modules\GradeDeadline\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\GradeDeadline\Http\Requests\GradeDeadlineSaveRequest;
use App\Modules\GradeDeadline\Http\Resources\GradeDeadlineDataTableItemsResource;
use App\Modules\GradeDeadline\Models\GradeDeadline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeDeadlineController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {
            $items = GradeDeadline::join('periods', 'grade_deadlines.period_id', '=', 'periods.id')
                ->join('months', 'periods.month', '=', 'months.id')
                ->select(
                    'grade_deadlines.*',
                    DB::raw('CONCAT( periods.year, "-", upper(months.name)) as period')
                )
                ->orderBy('periods.month', 'desc')
                ->orderBy('grade_deadlines.id', 'desc')
                ->dataTable($request);
            GradeDeadlineDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function save(GradeDeadlineSaveRequest $request)
    {
        try {
            $data = $request->validated();
            if ($request->id) {
                GradeDeadline::createExtension($data, $request->id);
            } else {
                $period = GradeDeadline::where('period_id', $data['period_id'])->where('type', 'REGULAR')->exists();
                if ($period) return ApiResponse::error(null, 'Ya existe un periodo de matrícula regular para este periodo');
                GradeDeadline::createRegular($data);
            }
            return ApiResponse::success(null, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }

    //activeGradePeriod


    public function getActiveGradePeriod()
    {
        try {
            $period = GradeDeadline::activeEnrollmentPeriod();
            if (!$period) {
                return ApiResponse::warning(null, 'No existe un periodo de matrícula activo');
            }
            return ApiResponse::success($period);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Ocurrió un error al obtener el periodo de matrícula activo');
        }
    }
}
