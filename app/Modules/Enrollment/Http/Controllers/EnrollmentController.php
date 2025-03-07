<?php

namespace App\Modules\Enrollment\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\Course\Models\Course;
use App\Modules\CoursePrice\Models\CoursePrice;
use App\Modules\Enrollment\Http\Requests\ModuleStoreRequest;
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
                APIResponse::error(null, 'No hay un periodo de matrícula activo');
            }

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

            $group = Group::select('course_id', 'modality')
                ->where('id', $request->groupId)
                ->first();

            $coursePrice = CoursePrice::select('presential_price', 'virtual_price')
                ->where('course_id', $group->course_id)
                ->where('student_type_id', $student->student_type_id)
                ->first();

            $price = $group->modality == 'PRESENCIAL' ? $coursePrice->presential_price : $coursePrice->virtual_price;

            if ($totalPayment < $price) {
                throw new \Exception('El monto de los pagos no cubre el precio del grupo');
            }

            $enrollmentGroup = EnrollmentGroup::create([
                'student_id' => $request->studentId,
                'group_id' => $request->groupId,
                'period_id' => $period->id,
            ]);

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

    public function getStudentEnrollmentAvaliable(Request $request)
    {
        try {
            $exists = Student::exists($request->studentId);
            if (!$exists) return ApiResponse::error('No se encontró el estudiante', 'No se encontró el estudiante');

            $enrollments = Enrollment::select(
                // 'enrollments.id',
                'modules.id as moduleId',
                'modules.name as moduleName',
            )
                ->distinct()
                ->join('modules', function ($join) {
                    $join->on('enrollments.module_id', '=', 'modules.id')
                        ->orWhere('modules.is_extracurricular', true);
                })
                ->where('modules.curriculum_id', $request->curriculumId)
                ->where('enrollments.student_id', $request->studentId)
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
            ->join('module_prices', function ($join) {
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

    //STUDENT ENROLLMENT
    // public function storeStudentEnrollment(Request $request)
    // {
    //     try {
    //         //bucar estudiante 
    //         $user = Auth::user();

    //         $student = Student::select('students.id')
    //             ->join('people', 'students.person_id', '=', 'people.id')
    //             ->where('people.document_number', $user->username)
    //             ->first();

    //         if (!$student) {
    //             return ApiResponse::error('No se encontró el estudiante', 'No se encontró el estudiante');
    //         }

    //         //validar pago
    //         $paymentData = [
    //             'studentId' => $student->id,
    //             'amount' => (float) $request->paymentAmount,
    //             'date' => $request->paymentDate,
    //             'sequenceNumber' => $request->paymentSequence,
    //             'paymentTypeId' => $request->paymentMethod,
    //         ];

    //         $payment = $this->validatePayment($paymentData);
    //         $payment = Crypt::decrypt($payment);

    //         //marcar como utilizado el pago 
    //         $payment = Payment::find($payment);
    //         $payment->is_enabled = true;
    //         $payment->save();

    //         $data = [
    //             'student_id' => $student->id,
    //             'module_id' => $request->moduleId,
    //             'curriculum_id' => $request->curriculumId,
    //             'payment_id' => $payment->id,
    //         ];

    //         Enrollment::create($data);

    //         return ApiResponse::success(null, 'Matricula exitosa');
    //     } catch (\Exception $e) {
    //         return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
    //     }
    // }
    // //get enalble groups by user authenticated

    // public function enabledGroupsEnrollment(Request $request)
    // {
    //     try {

    //         $user = Auth::user();

    //         $student = Student::select(
    //             'students.id',
    //             'student_type_id',
    //         )
    //             ->join('people', 'students.person_id', '=', 'people.id')
    //             ->where('people.document_number', $user->username)
    //             ->first();

    //         $period = Period::where('is_enabled', true)->first();

    //         $enrollmentGroups = Group::select(
    //             'groups.id',
    //             'groups.name as group',
    //             'groups.modality as modality',
    //             DB::raw('IF(groups.modality = "PRESENCIAL", course_prices.presential_price, course_prices.virtual_price) as price'),
    //             'laboratories.name as laboratory',
    //             DB::raw('CONCAT(people.name, " ", people.last_name_father, " ", people.last_name_mother) as teacher'),
    //         )
    //             ->join('periods', 'groups.period_id', '=', 'periods.id')
    //             ->join('curriculum_courses', 'groups.curriculum_course_id', '=', 'curriculum_courses.id')
    //             ->join('course_prices', 'course_prices.course_id', '=', 'curriculum_courses.course_id')
    //             ->leftJoin('laboratories', 'groups.laboratory_id', '=', 'laboratories.id')
    //             ->leftJoin('teachers', 'groups.teacher_id', '=', 'teachers.id')
    //             ->leftJoin('people', 'teachers.person_id', '=', 'people.id')
    //             ->where('course_prices.student_type_id', $student->student_type_id)
    //             ->where('curriculum_courses.id', $request->curriculumCourseId)
    //             ->where('periods.id', $period->id)
    //             ->where('groups.is_enabled', true)
    //             ->get()
    //             ->map(function ($group) use ($request) {
    //                 $group['schedules'] = Schedule::select(
    //                     'schedules.day as day',
    //                     'schedules.start_hour as startHour',
    //                     'schedules.end_hour as endHour',
    //                 )
    //                     ->where('schedules.group_id', $group->id)
    //                     ->get();
    //                 return $group;
    //             });

    //         return ApiResponse::success($enrollmentGroups, 'Registros cargados correctamente');
    //     } catch (\Exception $e) {
    //         return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
    //     }
    // }
    // public function storeGroupEnrollment(Request $request)
    // {
    //     try {

    //         DB::beginTransaction();

    //         $user = Auth::user();

    //         $student = Student::select('students.id')
    //             ->join('people', 'students.person_id', '=', 'people.id')
    //             ->where('people.document_number', $user->username)
    //             ->first();

    //         if (!$student) {
    //             return ApiResponse::error('No se encontró el estudiante', 'No se encontró un estudiante asociado a su usuario');
    //         }

    //         //validar pago
    //         $paymentData = [
    //             'studentId' => $student->id,
    //             'amount' => (float) $request->paymentAmount,
    //             'date' => $request->paymentDate,
    //             'sequenceNumber' => $request->paymentSequence,
    //             'paymentTypeId' => $request->paymentMethod,
    //         ];

    //         $payment = $this->validatePayment($paymentData);
    //         $payment = Crypt::decrypt($payment);

    //         //marcar como utilizado el pago 
    //         $payment = Payment::find($payment);
    //         $payment->is_enabled = true;
    //         $payment->save();

    //         $period = Period::where('is_enabled', true)
    //             ->where('enrollment_enabled', true)
    //             ->first();

    //         if (!$period) {
    //             return ApiResponse::error('No se encontró el periodo de matrícula', 'No se encontró el periodo de matrícula');
    //         }

    //         $data = [
    //             'student_id' => $student->id,
    //             'group_id' => $request->groupId,
    //             'period_id' => $period->id,
    //             'payment_id' => $payment->id,
    //         ];

    //         EnrollmentGroup::create($data);

    //         DB::commit();
    //         return ApiResponse::success(null, 'Matricula exitosa');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
    //     }
    // }
}
