<?php

namespace App\Modules\Enrollment\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Modules\CurriculumCourse\Models\CurriculumCourse;
use App\Modules\Enrollment\Models\Enrollment;
use App\Modules\EnrollmentGroup\Models\EnrollmentGroup;
use App\Modules\Group\Models\Group;
use App\Modules\Student\Models\Student;
use App\Modules\Payment\Models\Payment;
use App\Modules\Period\Models\Period;
use App\Modules\PreEnrollment\Models\PreEnrollment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

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

            $preEnrollment = PreEnrollment::create([
                'student_id' => $request->studentId,
                'group_id' => $request->groupId,
                'period_id' => $period->id,
            ]);

            EnrollmentGroup::create([
                'pre_enrollment_id' => $preEnrollment->id,
                'payment_id' => $payment->id,
            ]);

            DB::commit();
            return ApiResponse::success(null, 'Grupo matriculado correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al matricular el grupo');
        }
    }

    public function getStudentEnrollment(Request $request)
    {
        try {
            $exists = Student::exists($request->id);
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
                ->where('students.id', $request->id)
                ->first();


            $student['enrollments'] = $student->enrollments()->select(
                'enrollments.id',
                'modules.name as moduleName',
                'modules.id as moduleId',
            )
                ->distinct()
                ->join('modules', 'enrollments.module_id', '=', 'modules.id')
                ->join('curriculum_courses', 'enrollments.module_id', '=', 'curriculum_courses.module_id')
                ->where('curriculum_courses.curriculum_id', $request->curriculumId)
                ->get()->map(function ($enrollment) use ($request) {

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
                        ->get()
                        ->map(function ($course) use ($request) {
                            $course['enrollmentCourse'] = EnrollmentGroup::select(
                                'enrollment_groups.id',
                                'groups.name as groupName',
                                'periods.year as periodYear',
                                'periods.month as periodMonth',
                                'enrollment_grades.grade as grade',
                            )->join('pre_enrollments', 'enrollment_groups.pre_enrollment_id', '=', 'pre_enrollments.id')
                                ->join('groups', 'pre_enrollments.group_id', '=', 'groups.id')
                                ->join('periods', 'groups.period_id', '=', 'periods.id')
                                ->leftJoin('enrollment_grades', 'enrollment_groups.id', '=', 'enrollment_grades.enrollment_group_id')
                                ->where('groups.curriculum_course_id', $course->id)
                                ->where('pre_enrollments.student_id', $request->id)
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
                DB::raw('CONCAT_WS(" ", modules.name, "( S/.", prices.enrollment_price, ")") as label')
            )
                ->distinct()
                ->join('modules', 'curriculum_courses.module_id', '=', 'modules.id')
                ->join('prices', 'prices.module_id', '=', 'modules.id')
                ->where('curriculum_courses.curriculum_id', $request->curriculumId)
                ->where('prices.student_type_id', $student->student_type_id)
                ->whereNotIn('modules.id', function ($query) use ($request) {
                    $query->select('enrollments.module_id')
                        ->from('enrollments')
                        ->where('enrollments.student_id', $request->studentId);
                })
                ->where('curriculum_courses.is_enabled', true)
                ->get();

            return ApiResponse::success($modules, 'Registros cargados correctamente');
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
        //TODO: SERVICE PAYMENT VALIDATION 

        return  true;
    }

    //enrollment/enabled-groups
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
                'groups.id as value',
                DB::raw('CONCAT_WS(" ", groups.name, "(S/.", prices.presential_price, ")") as label')
                //precio de matricula
            )
                ->join('periods', 'groups.period_id', '=', 'periods.id')
                ->join('curriculum_courses', 'groups.curriculum_course_id', '=', 'curriculum_courses.id')
                ->join('prices', 'prices.module_id', '=', 'curriculum_courses.module_id')
                ->where('prices.student_type_id', $student->student_type_id)
                ->where('periods.id', $period->id)
                ->where('curriculum_courses.id', $request->curriculumCourseId)
                ->get();

            return ApiResponse::success($enrollmentGroups, 'Registros cargados correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }
}
