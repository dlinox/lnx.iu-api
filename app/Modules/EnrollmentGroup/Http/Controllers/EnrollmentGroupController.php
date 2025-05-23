<?php

namespace App\Modules\EnrollmentGroup\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Mail\ConfirmEnrolledMail;
use App\Modules\CoursePrice\Models\CoursePrice;
use App\Modules\EnrollmentGroup\Http\Resources\EnrollmentGroupDataTableItemsResource;
use App\Modules\EnrollmentGroup\Models\EnrollmentGroup;
use App\Modules\Group\Models\Group;
use App\Modules\Schedule\Models\Schedule;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EnrollmentGroupController extends Controller
{
    public function loadDataTable(Request $request)
    {
        try {

            $items = Group::select(
                'groups.id as id',
                'groups.name as group',
                'groups.modality as modality',
                'groups.min_students as minStudents',
                'groups.max_students as maxStudents',
                'groups.status as status',
                'modules.name as module',
                'areas.name as area',
                'courses.name as course',
                'teachers.id as teacher_id',
                DB::raw('CONCAT_WS(" ", teachers.name, teachers.last_name_father, teachers.last_name_mother) as teacher'),
                'laboratories.id as laboratory_id',
                'laboratories.name as laboratory',
            )
                ->join('courses', 'courses.id', '=', 'groups.course_id')
                ->join('areas', 'areas.id', '=', 'courses.area_id')
                ->join('modules', 'modules.id', '=', 'courses.module_id')
                ->leftJoin('teachers', 'teachers.id', '=', 'groups.teacher_id')
                ->leftJoin('laboratories', 'laboratories.id', '=', 'groups.laboratory_id')
                ->where('groups.period_id', $request->filters['periodId'])
                ->orderBy('groups.period_id', 'desc')
                ->dataTable($request, [
                    'groups.name',
                    'groups.modality',
                    'groups.status',
                    'modules.name',
                    'areas.name',
                    'courses.name'
                ]);
            EnrollmentGroupDataTableItemsResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }
    public function changeStatusGroup(Request $request)
    {
        try {
            $group = Group::find($request->id);
            if (!$group) return ApiResponse::error(null, 'No se encontró el registro');
            $group->status = $request->status;
            $group->save();
            return ApiResponse::success(null, 'Estado actualizado correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al actualizar el estado');
        }
    }

    public function getStudents(Request $request)
    {
        try {
            $items = EnrollmentGroup::select(
                'enrollment_groups.id as id',
                'students.id as student_id',
                DB::raw('CONCAT_WS(" ", students.name, students.last_name_father, students.last_name_mother) as student'),
                'students.email as email',
                'students.phone as phone',
                'enrollment_groups.status as status'
            )
                ->join('students', 'students.id', '=', 'enrollment_groups.student_id')
                ->where('enrollment_groups.group_id', $request->groupId)
                ->where('enrollment_groups.status', '=', 'MATRICULADO')
                ->get();

            return ApiResponse::success($items, 'Estudiantes obtenidos correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al obtener los estudiantes');
        }
    }


    public function sendMassiveEmail(Request $request)
    {
        try {
            $group = Group::find($request->groupId);
            if (!$group) return ApiResponse::error(null, 'No se encontró el registro');
            $students = EnrollmentGroup::select(
                'enrollment_groups.id as enrollmentGroupId',
                'students.id as student_id',
                DB::raw('CONCAT_WS(" ", students.name, students.last_name_father) as student'),
                'students.email as email',
            )
                ->join('students', 'students.id', '=', 'enrollment_groups.student_id')
                ->where('enrollment_groups.group_id', $request->groupId)
                ->where('enrollment_groups.status', '=', 'MATRICULADO')
                ->where('students.email', 'REGEXP', '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$')
                ->get();

            if ($students->isEmpty()) return ApiResponse::error(null, 'No se encontraron estudiantes matriculados en el grupo');

            foreach ($students as $student) {
                $attachment = $this->generateEnrollmentPDF($student->enrollmentGroupId);
                Mail::to($student->email)->send(new ConfirmEnrolledMail($student, $attachment));
            }
            return ApiResponse::success(null, 'Correos enviados correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al enviar los correos');
        }
    }

    private function generateEnrollmentPDF($enrollmentGroupsId)
    {
        $enrollment = EnrollmentGroup::select(
            'enrollment_groups.id',
            'students.document_number as documentNumber',
            DB::raw('CONCAT_WS(" ", students.name, students.last_name_father, students.last_name_mother) as student'),
            'student_types.id as studentTypeId',
            'student_types.name as studentType',
            'students.code as studentCode',
            'modules.name as module',
            'module_prices.price as modulePrice',
            'courses.id as courseId',
            'courses.name as course',
            'groups.id as groupId',
            'groups.name as group',
            'groups.modality as modality',
            DB::raw('GROUP_CONCAT(DISTINCT payment_types.name SEPARATOR ", ") as paymentType'),
            DB::raw('SUM(payments.amount) as paymentAmount'),
            DB::raw('GROUP_CONCAT(payments.sequence_number SEPARATOR ",") as paymentSequence'),
            DB::raw('CONCAT(upper(months.name), " ", periods.year) as period')
        )
            ->join('students', 'enrollment_groups.student_id', '=', 'students.id')
            ->join('groups', 'enrollment_groups.group_id', '=', 'groups.id')
            ->join('courses', 'groups.course_id', '=', 'courses.id')
            ->join('modules', 'courses.module_id', '=', 'modules.id')
            ->leftJoin('module_prices', function ($join) {
                $join->on('module_prices.module_id', '=', 'modules.id')
                    ->on('module_prices.student_type_id', '=', 'students.student_type_id');
            })
            ->join('periods', 'groups.period_id', '=', 'periods.id')
            ->join('months', 'periods.month', '=', 'months.id')
            ->join('payments', 'payments.enrollment_id', '=', 'enrollment_groups.id')
            ->join('payment_types', 'payments.payment_type_id', '=', 'payment_types.id')
            ->join('student_types', 'students.student_type_id', '=', 'student_types.id')
            ->where('enrollment_groups.id', $enrollmentGroupsId)
            ->groupBy(
                'enrollment_groups.id',
                'students.name',
                'students.last_name_father',
                'students.last_name_mother',
                'student_types.id',
                'student_types.name',
                'students.code',
                'modules.name',
                'courses.id',
                'courses.name',
                'groups.id',
                'groups.name',
                'groups.modality',
                'module_prices.price',
                'months.name',
                'periods.year'
            )
            ->first();

        $coursePrice = CoursePrice::select('presential_price', 'virtual_price')
            ->where('course_id', $enrollment->courseId)
            ->where('student_type_id', $enrollment->studentTypeId)
            ->first();

        $enrollment->coursePrice = $enrollment->modality == 'PRESENCIAL' ? $coursePrice->presential_price : $coursePrice->virtual_price;

        $enrollment->schedule = Schedule::byGroup($enrollment->groupId);

        $pdf = PDF::loadView('pdf.EnrollmentRecord', ['enrollment' => $enrollment]);

        $directory = storage_path('app/public/enrollment/');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $pdf->save($directory . $enrollment->studentCode . '-' . $enrollment->groupId . '-' . $enrollment->id . '.pdf');

        $url = asset('storage/enrollment/' . $enrollment->studentCode . '-' . $enrollment->groupId . '-' . $enrollment->id . '.pdf');

        return $url;
    }
}
