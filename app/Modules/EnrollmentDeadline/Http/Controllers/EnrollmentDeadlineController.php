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
            $items = EnrollmentDeadline::join('periods', 'enrollment_periods.period_id', '=', 'periods.id')
                ->join('view_month_constants', 'periods.month', '=', 'view_month_constants.value')
                ->select(
                    'enrollment_periods.*',
                    DB::raw('CONCAT( periods.year, "-",view_month_constants.label) as period')
                )
                ->orderBy('periods.month', 'desc')
                ->orderBy('enrollment_periods.id', 'desc')
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

                EnrollmentDeadline::createRegular($data);
            }
            return ApiResponse::success(null, 'Registro creado correctamente', 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
