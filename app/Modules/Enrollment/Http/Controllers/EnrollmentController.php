<?php

namespace App\Modules\Enrollment\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\Course\Models\Course;
use App\Modules\CoursePrice\Models\CoursePrice;
use App\Modules\Enrollment\Http\Requests\ModuleStoreRequest;
use App\Modules\Enrollment\Http\Resources\EnrollmentDataTableItemResource;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\EnrollmentGroup\Models\EnrollmentGroup;
use App\Modules\Group\Models\Group;
use App\Modules\Module\Models\Module;
use App\Modules\Student\Models\Student;
use App\Modules\Payment\Models\Payment;
use App\Modules\Period\Models\Period;
use App\Modules\Schedule\Models\Schedule;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class EnrollmentController extends Controller
{

    public function loadDataTable(Request $request)
    {
        try {

            $items = EnrollmentGroup::select(
                'enrollment_groups.id as id',
                'modules.name as module',
                'enrollment_groups.status as enrollmentStatus',
                'groups.id as groupId',
                'groups.name as group',
                'groups.modality as modality',
                'laboratories.name as laboratory',
                'students.id as studentId',
                'courses.id as courseId',
                'courses.curriculum_id as curriculumId',
                DB::raw('CONCAT_WS(" ", people.name, people.last_name_father, people.last_name_mother) as student'),
                DB::raw('CONCAT_WS("-", periods.year, view_month_constants.label) as period'),
                DB::raw('CONCAT_WS("- ", courses.code, courses.name) as course'),
            )
                ->join('groups', 'enrollment_groups.group_id', '=', 'groups.id')
                ->join('students', 'enrollment_groups.student_id', '=', 'students.id')
                ->join('people', 'students.person_id', '=', 'people.id')
                ->join('courses', 'groups.course_id', '=', 'courses.id')
                ->join('modules', 'courses.module_id', '=', 'modules.id')
                ->join('areas', 'courses.area_id', '=', 'areas.id')
                ->join('periods', 'enrollment_groups.period_id', '=', 'periods.id')
                ->join('view_month_constants', 'periods.month', '=', 'view_month_constants.value')
                ->leftJoin('laboratories', 'groups.laboratory_id', '=', 'laboratories.id')
                // ->whereRaw('created_by in (Select id from users where users.account_level = "student")')
                ->orderBy('periods.year', 'desc')
                ->orderBy('periods.month', 'desc')
                ->dataTable($request, [
                    'courses.name',
                    'groups.name',
                    'modules.name',
                    'people.name',
                ]);

            EnrollmentDataTableItemResource::collection($items);
            return ApiResponse::success($items);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }
    public function enrollmentModuleStore(ModuleStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $paymentsIds = array_map(function ($payment) {
                return Crypt::decrypt($payment);
            }, $request->payments);

            $totalPayment = Payment::whereIn('id', array_unique($paymentsIds))
                ->where('student_id', $request->studentId)
                ->where('is_used', false)
                ->sum('amount');

            $student = Student::select('student_type_id')
                ->where('id', $request->studentId)
                ->first();

            $modulePrice = Module::join('module_prices', 'module_prices.module_id', '=', 'modules.id')
                ->where('modules.id', $request->moduleId)
                ->where('module_prices.student_type_id', $student->student_type_id)
                ->first();

            if ($totalPayment < $modulePrice->price) {
                throw new \Exception('El monto de los pagos no cubre el precio del módulo');
            }

            $data = $request->validated();

            $enrollment = Enrollment::create($data);

            foreach ($paymentsIds as $paymentId) {
                $payment = Payment::find($paymentId);
                $payment->enrollment_type = 'M';
                $payment->enrollment_id = $enrollment->id;
                $payment->is_used = true;
                $payment->save();
            }

            DB::commit();
            return ApiResponse::success(null, 'Módulo matriculado correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al matricular el módulo');
        }
    }

    public function enrollmentGroupStore(Request $request)
    {
        try {
            DB::beginTransaction();

            $period = Period::where('status', 'MATRICULA')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->first();

            if (!$period) {
                DB::rollBack();
                return ApiResponse::error(null, 'No hay un periodo de matrícula activo');
            }

            $paymentsIds = array_map(function ($payment) {
                return Crypt::decrypt($payment);
            }, $request->payments);


            if ($request->id) {
                $totalPayment = Payment::whereIn('id', array_unique($paymentsIds))
                    ->where('student_id', $request->studentId)
                    ->where('enrollment_id', $request->id)
                    ->sum('amount');
            } else {
                $totalPayment = Payment::whereIn('id', array_unique($paymentsIds))
                    ->where('student_id', $request->studentId)
                    ->where('is_used', false)
                    ->sum('amount');
            }

            $student = Student::select('student_type_id')
                ->where('id', $request->studentId)
                ->first();

            $group = Group::select('course_id', 'modality')
                ->where('id', $request->groupId)
                ->first();

            $coursePrice = CoursePrice::select('presential_price', 'virtual_price')
                ->where('course_id', $group->course_id)
                ->where('student_type_id', $student->student_type_id)
                ->first();

            $price = $group->modality == 'PRESENCIAL' ? $coursePrice->presential_price : $coursePrice->virtual_price;

            if ($totalPayment < $price) {
                DB::rollBack();
                return  ApiResponse::error(null, 'El monto de los pagos no cubre el precio del grupo');
            }

            if ($request->id) {
                $enrollmentGroup = EnrollmentGroup::find($request->id);
                $enrollmentGroup->status = 'MATRICULADO';
                $enrollmentGroup->save();
            } else {
                $enrollmentGroup = EnrollmentGroup::create([
                    'student_id' => $request->studentId,
                    'group_id' => $request->groupId,
                    'period_id' => $period->id,
                ]);
            }

            foreach ($paymentsIds as $paymentId) {
                $payment = Payment::find($paymentId);
                $payment->enrollment_type = 'G';
                $payment->enrollment_id = $enrollmentGroup->id;
                $payment->is_used = true;
                $payment->save();
            }

            DB::commit();
            return ApiResponse::success(null, 'Matriculado correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al matricular el grupo');
        }
    }

    public function enrollmentGroupUpdate(Request $request)
    {
        try {

            $period = Period::where('status', 'MATRICULA')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->first();

            if (!$period) return ApiResponse::error(null, 'No hay un periodo de matrícula activo, para realizar la actualización');

            $paymentsIds = array_map(function ($payment) {
                return Crypt::decrypt($payment);
            }, $request->payments);

            $enrollmentGroup = EnrollmentGroup::find($request->id);

            if (!$enrollmentGroup) return ApiResponse::error(null, 'No se encontró el registro');

            $currentPayments = Payment::where('enrollment_id', $request->id)->get()->pluck('id')->toArray();

            $paymentsIds = array_diff($paymentsIds, $currentPayments);

            $totalNewPayment = Payment::where('student_id', $request->studentId)
                ->whereIn('id', array_unique($paymentsIds))
                ->sum('amount');

            $totalCurrentPayment = Payment::whereIn('id', $currentPayments)
                ->where('student_id', $request->studentId)
                ->sum('amount');

            $totalPayment = $totalNewPayment + $totalCurrentPayment;

            $student = Student::select('student_type_id')
                ->where('id', $request->studentId)
                ->first();

            $group = Group::select('course_id', 'modality')
                ->where('id', $request->groupId)
                ->first();

            $coursePrice = CoursePrice::select('presential_price', 'virtual_price')
                ->where('course_id', $group->course_id)
                ->where('student_type_id', $student->student_type_id)
                ->first();

            $price = $group->modality == 'PRESENCIAL' ? $coursePrice->presential_price : $coursePrice->virtual_price;

            if ($totalPayment < $price) {
                return  ApiResponse::error(null, 'El monto de los pagos no cubre el precio del grupo');
            }

            DB::beginTransaction();

            $enrollmentGroup->group_id = $request->groupId;
            $enrollmentGroup->status = 'MATRICULADO';
            $enrollmentGroup->save();

            foreach ($paymentsIds as $paymentId) {
                $payment = Payment::find($paymentId);
                $payment->enrollment_type = 'G';
                $payment->enrollment_id = $enrollmentGroup->id;
                $payment->is_used = true;
                $payment->save();
            }

            DB::commit();
            return ApiResponse::success(null, 'Matriculado correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al matricular el grupo');
        }
    }

    //reserved
    public function enrollmentGroupReserved(Request $request)
    {

        try {

            $period = Period::where('status', 'MATRICULA')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->first();

            if (!$period) return ApiResponse::error(null, 'No hay un periodo de matrícula activo, para realizar la reserva');

            $enrollmentGroup = EnrollmentGroup::find($request->id);

            if (!$enrollmentGroup) return ApiResponse::error(null, 'No se encontró el registro');

            $enrollmentGroup->status = 'RESERVADO';
            $enrollmentGroup->save();

            return ApiResponse::success(null, 'Grupo reservado correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al reservar el grupo');
        }
    }
    //cancel
    public function enrollmentGroupCancel(Request $request)
    {
        try {

            $enrollmentGroup = EnrollmentGroup::find($request->id);

            if (!$enrollmentGroup) return ApiResponse::error(null, 'No se encontró el registro');

            $payments = Payment::where('enrollment_id', $request->id)
                ->where('enrollment_type', 'G')
                ->get();

            DB::beginTransaction();

            foreach ($payments as $payment) {
                $payment->enrollment_id = null;
                $payment->enrollment_type = null;
                $payment->is_used = false;
                $payment->save();
            }

            $enrollmentGroup->status = 'CANCELADO';
            $enrollmentGroup->save();

            DB::commit();
            return ApiResponse::success(null, 'Grupo cancelado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage(), 'Error al cancelar el grupo');
        }
    }

    public function getEnrollmentGroupPayments(Request $request)
    {

        try {
            $payments = Payment::where('enrollment_id', $request->id)
                ->where('enrollment_type', 'G')
                ->get()
                ->map(function ($payment) {
                    $payment['amount'] = number_format($payment->amount, 2);
                    $payment['sequenceNumber'] = $payment->sequence_number;
                    $payment['token'] = Crypt::encrypt($payment->id);
                    return $payment;
                });

            return ApiResponse::success($payments);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function getStudentEnrollmentAvaliable(Request $request)
    {
        try {
            $exists = Student::exists($request->studentId);
            if (!$exists) return ApiResponse::error('No se encontró el estudiante', 'No se encontró el estudiante');

            $enrollments = Enrollment::select(
                // 'enrollments.id',
                'modules.id as moduleId',
                'modules.name as moduleName',
                'modules.is_extracurricular as isExtracurricular',
            )
                ->distinct()
                ->join('modules', function ($join) {
                    $join->on('enrollments.module_id', '=', 'modules.id')
                        ->orWhere('modules.is_extracurricular', true);
                })
                ->where('modules.curriculum_id', $request->curriculumId)
                ->where('enrollments.student_id', $request->studentId)
                ->orderBy('modules.is_extracurricular', 'asc')
                ->orderBy('modules.name', 'asc')
                // ->where('modules.is_extracurricular', false)
                ->get()->map(function ($enrollment) use ($request) {
                    $courses = Course::select(
                        'courses.id',
                        'courses.name',
                        'courses.code',
                        'areas.name as area',
                    )
                        ->join('areas', 'courses.area_id', '=', 'areas.id')
                        ->where('courses.module_id', $enrollment->moduleId)
                        ->where('courses.is_enabled', true)
                        ->get()->map(function ($course) use ($request) {
                            $enrollmentGroups = EnrollmentGroup::select(
                                'enrollment_groups.id',
                                'enrollment_groups.status as enrollmentStatus',
                                'groups.id as groupId',
                                'groups.name as groupName',
                                'groups.status as groupStatus',
                                'groups.modality as groupModality',
                                'enrollment_grades.grade as grade',
                                DB::raw('CONCAT(periods.year,"-",view_month_constants.label) as period'),
                            )
                                ->leftJoin('enrollment_grades', 'enrollment_groups.id', '=', 'enrollment_grades.enrollment_group_id')
                                ->join('groups', 'enrollment_groups.group_id', '=', 'groups.id')
                                ->join('periods', 'groups.period_id', '=', 'periods.id')
                                ->join('view_month_constants', 'periods.month', '=', 'view_month_constants.value')
                                ->where('groups.course_id', $course->id)

                                ->where('enrollment_groups.student_id', $request->studentId)
                                ->get();

                            $course['enrollmentGroups'] = $enrollmentGroups;
                            return $course;
                        });
                    $enrollment['courses'] = $courses;
                    return $enrollment;
                });

            $data = [
                'enrollments' => $enrollments,
            ];
            return ApiResponse::success($data);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function getModulesEnrollment(Request $request)
    {
        try {

            $student = Student::select(
                'students.id',
                'student_type_id',
            )
                ->where('students.id', $request->studentId)
                ->first();

            $modules = Module::select(
                'modules.id as value',
                DB::raw('CONCAT_WS(" ", modules.name, "( S/.", module_prices.price, ")") as label'),
                'module_prices.price as price',
            )
                ->join('module_prices', 'module_prices.module_id', '=', 'modules.id')
                ->where('modules.curriculum_id', $request->curriculumId)
                ->where('module_prices.student_type_id', $student->student_type_id)
                ->whereNotIn('modules.id', function ($query) use ($request) {
                    $query->select('enrollments.module_id')
                        ->from('enrollments')
                        ->where('enrollments.student_id', $request->studentId);
                })
                ->get();
            return ApiResponse::success($modules);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function enabledGroups(Request $request)
    {
        try {
            $student = Student::select(
                'students.id',
                'student_type_id',
            )
                ->where('students.id', $request->studentId)
                ->first();

            $period = Period::where('status', 'MATRICULA')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->first();

            if (!$period) {
                return ApiResponse::error('No se encontró el periodo de matrícula', 'No se encontró el periodo de matrícula');
            }

            $enrollmentGroups = Group::select(
                'groups.id',
                'groups.name as group',
                'groups.modality as modality',
                DB::raw('IF(groups.modality = "PRESENCIAL", course_prices.presential_price, course_prices.virtual_price) as price'),
                'laboratories.name as laboratory',
                DB::raw('CONCAT(people.name, " ", people.last_name_father, " ", people.last_name_mother) as teacher'),
                'max_students as maxStudents',
                'min_students as minStudents',
                'groups.status as status',

            )
                ->join('periods', 'groups.period_id', '=', 'periods.id')
                ->join('courses', 'groups.course_id', '=', 'courses.id')
                ->join('course_prices', 'course_prices.course_id', '=', 'courses.id')
                ->leftJoin('laboratories', 'groups.laboratory_id', '=', 'laboratories.id')
                ->leftJoin('teachers', 'groups.teacher_id', '=', 'teachers.id')
                ->leftJoin('people', 'teachers.person_id', '=', 'people.id')
                ->where('course_prices.student_type_id', $student->student_type_id)
                ->where('courses.id', $request->courseId)
                ->where('periods.id', $period->id)
                ->whereIn('groups.status', ['ABIERTO', 'CERRADO'])
                ->get()
                ->map(function ($group) use ($request) {

                    $enrolledStudents = EnrollmentGroup::where('enrollment_groups.group_id', $group->id)
                        ->where('status', 'MATRICULADO')
                        ->count();

                    $reservedStudents = EnrollmentGroup::where('enrollment_groups.group_id', $group->id)
                        ->where('status', 'RESERVADO')
                        ->count();

                    $group['enrolledStudents'] = $enrolledStudents;
                    $group['reservedStudents'] = $reservedStudents;

                    $group['schedules'] = Schedule::select(
                        'schedules.day as day',
                        'schedules.start_hour as startHour',
                        'schedules.end_hour as endHour',
                    )
                        ->where('schedules.group_id', $group->id)
                        ->get();
                    return $group;
                });

            return ApiResponse::success($enrollmentGroups, 'Registros cargados correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function downloadEnrollmentPDF(Request $request)
    {
        $enrollment = EnrollmentGroup::select(
            'enrollment_groups.id',
            'people.document_number as documentNumber',
            DB::raw('CONCAT_WS(" ", people.name, people.last_name_father, people.last_name_mother) as student'),
            'student_types.name as studentType',
            'people.code as studentCode',
            'modules.name as module',
            'module_prices.price as modulePrice',
            'courses.name as course',
            'groups.name as group',
            'groups.modality as modality',
            DB::raw('GROUP_CONCAT(DISTINCT payment_types.name SEPARATOR ", ") as paymentType'),
            DB::raw('SUM(payments.amount) as paymentAmount'),
            DB::raw('GROUP_CONCAT(payments.sequence_number SEPARATOR ",") as paymentSequence'),
            DB::raw('CONCAT(view_month_constants.label, " ", periods.year) as period')
        )
            ->join('students', 'enrollment_groups.student_id', '=', 'students.id')
            ->join('people', 'students.person_id', '=', 'people.id')
            ->join('groups', 'enrollment_groups.group_id', '=', 'groups.id')
            ->join('courses', 'groups.course_id', '=', 'courses.id')
            ->join('modules', 'courses.module_id', '=', 'modules.id')
            ->leftJoin('module_prices', function ($join) {
                $join->on('module_prices.module_id', '=', 'modules.id')
                    ->on('module_prices.student_type_id', '=', 'students.student_type_id');
            })
            ->join('periods', 'groups.period_id', '=', 'periods.id')
            ->join('view_month_constants', 'periods.month', '=', 'view_month_constants.value')
            ->join('payments', 'payments.enrollment_id', '=', 'enrollment_groups.id')
            ->join('payment_types', 'payments.payment_type_id', '=', 'payment_types.id')
            ->join('student_types', 'students.student_type_id', '=', 'student_types.id')
            ->where('enrollment_groups.id', $request->id)
            // Agrupar por los datos principales (excluyendo los de "payments")
            ->groupBy(
                'enrollment_groups.id',
                'people.name',
                'people.last_name_father',
                'people.last_name_mother',
                'student_types.name',
                'people.code',
                'modules.name',
                'courses.name',
                'groups.name',
                'groups.modality',
                'module_prices.price',
                'view_month_constants.label',
                'periods.year'
            )
            ->first();

        $pdf = PDF::loadView('pdf.EnrollmentRecord', ['enrollment' => $enrollment]);
        return $pdf->output();
        // return ApiResponse::success($enrollment);
    }

    //`/enrollment/student-enrollment-avaliable-special`,

    public function getStudentEnrollmentAvaliableSpacial(Request $request)
    {
        try {
            $exists = Student::exists($request->studentId);
            if (!$exists) return ApiResponse::error('No se encontró el estudiante', 'No se encontró el estudiante');

            $enrollments = Module::select(
                'modules.id as moduleId',
                'modules.name as moduleName',
                'modules.is_extracurricular',
            )
                ->distinct()
                // ->join('modules', 'enrollments.module_id', '=', 'modules.id')
                ->where('modules.curriculum_id', $request->curriculumId)
                ->where('modules.is_extracurricular', false)
                ->orderBy('modules.name', 'asc')
                ->get()->map(function ($enrollment) use ($request) {
                    $courses = Course::select(
                        'courses.id',
                        'courses.name',
                        'courses.code',
                        'areas.name as area',
                    )
                        ->join('areas', 'courses.area_id', '=', 'areas.id')
                        ->where('courses.module_id', $enrollment->moduleId)
                        ->where('courses.is_enabled', true)
                        ->get()->map(function ($course) use ($request) {
                            $enrollmentGroups = EnrollmentGroup::select(
                                'enrollment_groups.id',
                                'enrollment_groups.status as enrollmentStatus',
                                'groups.id as groupId',
                                'groups.name as groupName',
                                'groups.status as groupStatus',
                                'groups.modality as groupModality',
                                'enrollment_grades.grade as grade',
                                DB::raw('CONCAT(periods.year,"-",view_month_constants.label) as period'),
                            )
                                ->leftJoin('enrollment_grades', 'enrollment_groups.id', '=', 'enrollment_grades.enrollment_group_id')
                                ->join('groups', 'enrollment_groups.group_id', '=', 'groups.id')
                                ->join('periods', 'groups.period_id', '=', 'periods.id')
                                ->join('view_month_constants', 'periods.month', '=', 'view_month_constants.value')
                                ->where('groups.course_id', $course->id)

                                ->where('enrollment_groups.student_id', $request->studentId)
                                ->get();

                            $course['enrollmentGroups'] = $enrollmentGroups;
                            return $course;
                        });
                    $enrollment['courses'] = $courses;
                    return $enrollment;
                });

            $data = [
                'enrollments' => $enrollments,
            ];
            return ApiResponse::success($data);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }
}
