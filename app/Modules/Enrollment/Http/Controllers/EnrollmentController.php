<?php

namespace App\Modules\Enrollment\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\CurriculumCourse\Models\CurriculumCourse;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\EnrollmentGroup\Models\EnrollmentGroup;
use App\Modules\Group\Models\Group;
use App\Modules\Module\Models\Module;
use App\Modules\Student\Models\Student;
use App\Modules\Payment\Models\Payment;
use App\Modules\Period\Models\Period;
use App\Modules\Schedule\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class EnrollmentController extends Controller
{

    public function enrollmentModuleStore(Request $request)
    {
        try {
            DB::beginTransaction();
            $payment = Crypt::decrypt($request->payment[0]['token']);
            $payment = Payment::find($payment);
            if (!$payment) {
                throw new \Exception('Token de pago inválido');
            }
            $payment->is_enabled = true;
            $payment->save();

            Enrollment::create([
                'curriculum_id' => $request->curriculumId,
                'student_id' => $request->studentId,
                'module_id' => $request->module,
                'payment_id' => $payment->id,
            ]);

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
            $payment = Crypt::decrypt($request->payment[0]['token']);
            $payment = Payment::find($payment);
            if (!$payment) {
                throw new \Exception('Token de pago inválido');
            }
            $payment->is_enabled = true;
            $payment->save();

            $period = Period::where('is_enabled', true)->first();

            EnrollmentGroup::create([
                'student_id' => $request->studentId,
                'group_id' => $request->groupId,
                'period_id' => $period->id,
                'payment_id' => $payment->id,
            ]);

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
            if (!$exists) {
                return ApiResponse::error('No se encontró el estudiante', 'No se encontró el estudiante');
            }

            $student = Student::select(
                'students.id',
                'people.code',
                'people.document_number as documentNumber',
                'people.name',
                'people.last_name_father as lastNameFather',
                'people.last_name_mother as lastNameMother',
                'people.email',
                'people.phone',
                'student_types.name as studentType',
                'document_types.name as documentType',
                'students.is_enabled as isEnabled'
            )
                ->join('people', 'students.person_id', '=', 'people.id')
                ->join('student_types', 'students.student_type_id', '=', 'student_types.id')
                ->leftJoin('document_types', 'people.document_type_id', '=', 'document_types.id')
                ->where('students.id', $request->studentId)
                ->first();


            $student['enrollments'] =
                Module::select(
                    'modules.id',
                    'modules.id as moduleId',
                    'modules.name as moduleName',
                )
                ->distinct()
                ->join('curriculum_courses', 'modules.id', '=', 'curriculum_courses.module_id')
                ->where('curriculum_courses.curriculum_id', $request->curriculumId)
                ->where(function ($query) use ($request) {
                    $query->whereIn('modules.id', function ($query) use ($request) {
                        $query->select('enrollments.module_id')
                            ->from('enrollments')
                            ->where('enrollments.student_id', $request->studentId);
                    })->orWhere('curriculum_courses.is_extracurricular', true);
                })
                ->get()
                ->map(function ($enrollment) use ($request) {
                    $enrollment['courses'] = CurriculumCourse::select(
                        'curriculum_courses.id',
                        'curriculum_courses.order',
                        'courses.name as courseName',
                        'curriculum_courses.is_enabled as isEnabled'
                    )
                        ->join('courses', 'curriculum_courses.course_id', '=', 'courses.id')
                        ->where('curriculum_courses.module_id', $enrollment->moduleId)
                        ->where('curriculum_courses.is_enabled', true)
                        ->orderBy('curriculum_courses.order')
                        ->whereNotIn('courses.id', function ($query) use ($request) {
                            $query->select('curriculum_courses.course_id')
                                ->from('enrollment_groups')
                                ->join('groups', 'enrollment_groups.group_id', '=', 'groups.id')
                                ->join('curriculum_courses', 'groups.curriculum_course_id', '=', 'curriculum_courses.id')
                                ->join('periods', 'groups.period_id', '=', 'periods.id')
                                ->join('enrollment_grades', 'enrollment_groups.id', '=', 'enrollment_grades.enrollment_group_id')
                                ->where('enrollment_groups.student_id', $request->studentId)
                                ->where('enrollment_grades.grade', '>=', 11)
                                ->where('periods.id', '!=', $request->periodId);
                        })

                        ->get()
                        ->map(function ($course) use ($request) {
                            $course['enrollmentCourse'] = EnrollmentGroup::select(
                                'enrollment_groups.id',
                                'groups.name as groupName',
                                'periods.year as periodYear',
                                'periods.month as periodMonth',
                                //docente del grupo
                                DB::raw('CONCAT(people.name, " ", people.last_name_father, " ", people.last_name_mother) as teacher'),
                                //modalidad del grupo
                                'groups.modality as modality',
                                //depende de la modalidad muestra el precio //VIRTUAL O PRESENCIAL
                                'payments.amount as price',
                                //laboratorio del grupo
                                'laboratories.name as laboratory',
                                //nota del estudiante
                                'enrollment_grades.grade as grade',
                            )
                                ->join('groups', 'enrollment_groups.group_id', '=', 'groups.id')
                                ->join('periods', 'groups.period_id', '=', 'periods.id')
                                ->join('curriculum_courses', 'groups.curriculum_course_id', '=', 'curriculum_courses.id')
                                ->join('payments', 'enrollment_groups.payment_id', '=', 'payments.id')
                                ->leftJoin('laboratories', 'groups.laboratory_id', '=', 'laboratories.id')
                                ->leftJoin('teachers', 'groups.teacher_id', '=', 'teachers.id')
                                ->leftJoin('people', 'teachers.person_id', '=', 'people.id')
                                ->leftJoin('enrollment_grades', 'enrollment_groups.id', '=', 'enrollment_grades.enrollment_group_id')
                                ->where('groups.curriculum_course_id', $course->id)
                                ->where('enrollment_groups.student_id', $request->studentId)
                                ->where('periods.id', $request->periodId)
                                ->get();
                            return $course;
                        });

                    return $enrollment;
                });

            return ApiResponse::success($student, 'Registros cargados correctamente');
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


            $modules = CurriculumCourse::select(
                'modules.id as value',
                DB::raw('CONCAT_WS(" ", modules.name, "( S/.", module_prices.price, ")") as label'),
                'module_prices.price as price',
            )
                ->distinct()
                ->join('modules', 'curriculum_courses.module_id', '=', 'modules.id')
                ->join('module_prices', 'module_prices.module_id', '=', 'modules.id')
                ->where('curriculum_courses.curriculum_id', $request->curriculumId)
                ->where('module_prices.student_type_id', $student->student_type_id)
                ->whereNotIn('modules.id', function ($query) use ($request) {
                    $query->select('enrollments.module_id')
                        ->from('enrollments')
                        ->where('enrollments.student_id', $request->studentId);
                })
                ->where('curriculum_courses.is_enabled', true)
                ->get();

            return ApiResponse::success($modules);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function validatePaymentEnrollment(Request $request)
    {

        $request['date'] = Carbon::createFromTimestampMs($request->date)->format('Y-m-d');
        $payment = $this->validatePayment($request->all());

        return ApiResponse::success($payment, 'Pago validado correctamente');
    }

    private function validatePayment($data)
    {
        $validate = $this->_validatePaymentService($data);

        if (!$validate) {
            throw new \Exception('Error al validar el pago');
        }
        $payment = Payment::where('amount', $data['amount'])
            ->where('date', $data['date'])
            ->where('sequence_number', $data['sequenceNumber'])
            // ->where('payment_type_id', $data['paymentTypeId'])
            // ->where('student_id', $data['studentId'])
            ->first();

        if ($payment && $payment->student_id != $data['studentId']) {
            throw new \Exception('El pago ya fue registrado por otro estudiante');
        }

        if ($payment && $payment->is_enabled) {
            throw new \Exception('El pago ya fue utilizado');
        }

        if (!$payment) {
            $payment = Payment::create([
                'student_id' => $data['studentId'],
                'sequence_number' => $data['sequenceNumber'],
                'payment_type_id' => $data['paymentTypeId'],
                'amount' => $data['amount'],
                'date' => $data['date'],
                'is_enabled' => false,
            ]);
        }

        $paymentToken = Crypt::encrypt($payment->id);

        return $paymentToken;
    }

    private function _validatePaymentService($data)
    {
        return  true;
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

            $period = Period::where('is_enabled', true)->first();

            $enrollmentGroups = Group::select(
                'groups.id',
                'groups.name as group',
                'groups.modality as modality',
                DB::raw('IF(groups.modality = "PRESENCIAL", course_prices.presential_price, course_prices.virtual_price) as price'),
                'laboratories.name as laboratory',
                DB::raw('CONCAT(people.name, " ", people.last_name_father, " ", people.last_name_mother) as teacher'),
            )
                ->join('periods', 'groups.period_id', '=', 'periods.id')
                ->join('curriculum_courses', 'groups.curriculum_course_id', '=', 'curriculum_courses.id')
                ->join('course_prices', 'course_prices.course_id', '=', 'curriculum_courses.course_id')
                ->leftJoin('laboratories', 'groups.laboratory_id', '=', 'laboratories.id')
                ->leftJoin('teachers', 'groups.teacher_id', '=', 'teachers.id')
                ->leftJoin('people', 'teachers.person_id', '=', 'people.id')
                ->where('course_prices.student_type_id', $student->student_type_id)
                ->where('curriculum_courses.id', $request->curriculumCourseId)
                ->where('periods.id', $period->id)
                ->where('groups.is_enabled', true)
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

    public function downloadEnrollmentRecord(Request $request)
    {

        $enrollment = EnrollmentGroup::select(
            'enrollment_groups.id',
            'groups.name as group',
            DB::raw('CONCAT(view_month_constants.label, " ", periods.year) as period'),
            DB::raw('CONCAT_WS(" ", people.name, people.last_name_father, people.last_name_mother) as student'),
            'modules.name as module',
            'courses.name as course',
            'student_types.name as studentType',
        )
            ->join('groups', 'enrollment_groups.group_id', '=', 'groups.id')
            ->join('periods', 'groups.period_id', '=', 'periods.id')
            ->join('curriculum_courses', 'groups.curriculum_course_id', '=', 'curriculum_courses.id')
            ->join('modules', 'curriculum_courses.module_id', '=', 'modules.id')
            ->join('students', 'enrollment_groups.student_id', '=', 'students.id')
            ->join('student_types', 'students.student_type_id', '=', 'student_types.id')
            ->join('people', 'students.person_id', '=', 'people.id')
            ->join('view_month_constants', 'periods.month', '=', 'view_month_constants.id')
            ->join('payment_types', 'payments.payment_type_id', '=', 'payment_types.id')
            ->where('enrollment_groups.id', $request->id)
            ->first();


        $pdf = PDF::loadView('pdf.EnrollmentRecord', ['enrollment' => $enrollment]);
        return $pdf->output();
    }

    //STUDENT ENROLLMENT

    public function storeStudentEnrollment(Request $request)
    {
        try {
            //bucar estudiante 
            $user = Auth::user();

            $student = Student::select('students.id')
                ->join('people', 'students.person_id', '=', 'people.id')
                ->where('people.document_number', $user->username)
                ->first();

            if (!$student) {
                return ApiResponse::error('No se encontró el estudiante', 'No se encontró el estudiante');
            }

            //validar pago
            $paymentData = [
                'studentId' => $student->id,
                'amount' => (float) $request->paymentAmount,
                'date' => $request->paymentDate,
                'sequenceNumber' => $request->paymentSequence,
                'paymentTypeId' => $request->paymentMethod,
            ];

            $payment = $this->validatePayment($paymentData);
            $payment = Crypt::decrypt($payment);

            //marcar como utilizado el pago 
            $payment = Payment::find($payment);
            $payment->is_enabled = true;
            $payment->save();

            $data = [
                'student_id' => $student->id,
                'module_id' => $request->moduleId,
                'curriculum_id' => $request->curriculumId,
                'payment_id' => $payment->id,
            ];

            Enrollment::create($data);

            return ApiResponse::success(null, 'Matricula exitosa');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    //get enalble groups by user authenticated

    public function enabledGroupsEnrollment(Request $request)
    {
        try {

            $user = Auth::user();

            $student = Student::select(
                'students.id',
                'student_type_id',
            )
                ->join('people', 'students.person_id', '=', 'people.id')
                ->where('people.document_number', $user->username)
                ->first();

            $period = Period::where('is_enabled', true)->first();

            $enrollmentGroups = Group::select(
                'groups.id',
                'groups.name as group',
                'groups.modality as modality',
                DB::raw('IF(groups.modality = "PRESENCIAL", course_prices.presential_price, course_prices.virtual_price) as price'),
                'laboratories.name as laboratory',
                DB::raw('CONCAT(people.name, " ", people.last_name_father, " ", people.last_name_mother) as teacher'),
            )
                ->join('periods', 'groups.period_id', '=', 'periods.id')
                ->join('curriculum_courses', 'groups.curriculum_course_id', '=', 'curriculum_courses.id')
                ->join('course_prices', 'course_prices.course_id', '=', 'curriculum_courses.course_id')
                ->leftJoin('laboratories', 'groups.laboratory_id', '=', 'laboratories.id')
                ->leftJoin('teachers', 'groups.teacher_id', '=', 'teachers.id')
                ->leftJoin('people', 'teachers.person_id', '=', 'people.id')
                ->where('course_prices.student_type_id', $student->student_type_id)
                ->where('curriculum_courses.id', $request->curriculumCourseId)
                ->where('periods.id', $period->id)
                ->where('groups.is_enabled', true)
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
    public function storeGroupEnrollment(Request $request)
    {
        try {

            DB::beginTransaction();

            $user = Auth::user();

            $student = Student::select('students.id')
                ->join('people', 'students.person_id', '=', 'people.id')
                ->where('people.document_number', $user->username)
                ->first();

            if (!$student) {
                return ApiResponse::error('No se encontró el estudiante', 'No se encontró un estudiante asociado a su usuario');
            }

            //validar pago
            $paymentData = [
                'studentId' => $student->id,
                'amount' => (float) $request->paymentAmount,
                'date' => $request->paymentDate,
                'sequenceNumber' => $request->paymentSequence,
                'paymentTypeId' => $request->paymentMethod,
            ];

            $payment = $this->validatePayment($paymentData);
            $payment = Crypt::decrypt($payment);

            //marcar como utilizado el pago 
            $payment = Payment::find($payment);
            $payment->is_enabled = true;
            $payment->save();

            $period = Period::where('is_enabled', true)
                ->where('enrollment_enabled', true)
                ->first();

            if (!$period) {
                return ApiResponse::error('No se encontró el periodo de matrícula', 'No se encontró el periodo de matrícula');
            }

            $data = [
                'student_id' => $student->id,
                'group_id' => $request->groupId,
                'period_id' => $period->id,
                'payment_id' => $payment->id,
            ];

            EnrollmentGroup::create($data);

            DB::commit();
            return ApiResponse::success(null, 'Matricula exitosa');
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }
}
