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
use App\Modules\EnrollmentDeadline\Models\EnrollmentDeadline;
use App\Modules\EnrollmentGroup\Models\EnrollmentGroup;
use App\Modules\Group\Models\Group;
use App\Modules\Module\Models\Module;
use App\Modules\ModulePrice\Models\ModulePrice;
use App\Modules\Student\Models\Student;
use App\Modules\Payment\Models\Payment;
use App\Modules\Schedule\Models\Schedule;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{

    public function loadDataTable(Request $request)
    {
        try {

            $items = EnrollmentGroup::select(
                'enrollment_groups.id as id',
                'modules.name as module',
                'enrollment_groups.status as enrollmentStatus',
                'enrollment_groups.special_enrollment as isSpecial',
                'groups.id as groupId',
                'groups.name as group',
                'groups.modality as modality',
                'students.id as studentId',
                'courses.id as courseId',
                'courses.curriculum_id as curriculumId'
            )
                ->selectRaw('CONCAT_WS(" ", students.name, students.last_name_father, students.last_name_mother) as student')
                ->selectRaw('CONCAT_WS("-", periods.year, upper(months.name)) as period')
                ->selectRaw('CONCAT_WS("- ", courses.code, courses.name) as course')
                ->join('groups', 'enrollment_groups.group_id', '=', 'groups.id')
                ->join('students', 'enrollment_groups.student_id', '=', 'students.id')
                ->join('courses', 'groups.course_id', '=', 'courses.id')
                ->join('modules', 'courses.module_id', '=', 'modules.id')
                ->join('periods', 'enrollment_groups.period_id', '=', 'periods.id')
                ->join('months', 'periods.month', '=', 'months.id')
                ->when($request->filters['periodId'], function ($query) use ($request) {
                    $query->where('enrollment_groups.period_id', $request->filters['periodId']);
                })
                ->orderBy('enrollment_groups.id', 'desc')
                ->dataTable($request, [
                    'courses.name',
                    'modules.name',
                    'students.document_number',
                    'students.code',
                    'students.name',
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

            $user = Auth::user();

            $enrollmentPeriod = EnrollmentDeadline::activeEnrollmentPeriod();
            if (!$enrollmentPeriod)  ApiResponse::error(null, 'No se encontró el periodo de matrícula');

            $paymentsIds = array_map(function ($payment) {
                return Crypt::decrypt($payment);
            }, $request->payments);

            $student = Student::select('student_type_id')
                ->where('id', $request->studentId)
                ->first();

            if (!$student) return ApiResponse::error(null, 'No se encontró el estudiante');

            $totalPayment = Payment::whereIn('id', array_unique($paymentsIds))
                ->where('student_id', $request->studentId)
                ->where('is_used', false)
                ->sum('amount');


            $modulePrice = Module::join('module_prices', 'module_prices.module_id', '=', 'modules.id')
                ->where('modules.id', $request->moduleId)
                ->where('module_prices.student_type_id', $student->student_type_id)
                ->first();

            if (!$modulePrice) return ApiResponse::error(null, 'No se encontró el precio del módulo');

            $group = Group::select(
                'groups.id',
                'groups.course_id',
                'groups.modality',
                'course_prices.presential_price',
                'course_prices.virtual_price'
            )
                ->join('courses', 'courses.id', '=', 'groups.course_id')
                ->join('course_prices', 'course_prices.course_id', '=', 'courses.id')
                ->where('groups.id', $request->groupId)
                ->where('course_prices.student_type_id', $student->student_type_id)
                ->first();

            if (!$group) return ApiResponse::error(null, 'No se encontró el grupo');

            $groupPrice = $group->modality == 'PRESENCIAL' ? $group->presential_price : $group->virtual_price;

            if ($totalPayment < ($modulePrice->price + $groupPrice)) {
                return ApiResponse::error(null, 'El monto de los pagos no cubre el precio del módulo');
            }

            $shedules = Schedule::where('group_id', $group->id)->get();

            if ($shedules->count() == 0) return ApiResponse::error(null, 'El grupo no tiene horarios asignados');

            $enrolledSchedules = Schedule::select('schedules.id', 'schedules.start_hour', 'schedules.end_hour', 'schedules.day')
                ->join('groups', 'schedules.group_id', '=', 'groups.id')
                ->join('enrollment_groups', 'groups.id', '=', 'enrollment_groups.group_id')
                ->where('enrollment_groups.student_id', $student->id)
                ->where('enrollment_groups.period_id', $enrollmentPeriod['periodId'])
                ->where('enrollment_groups.status', 'MATRICULADO')
                ->get();


            foreach ($shedules as $shedule) {
                foreach ($enrolledSchedules as $enrolledShedule) {
                    if ($shedule->day == $enrolledShedule->day) {
                        $startHour = strtotime($shedule->start_hour);
                        $endHour = strtotime($shedule->end_hour);
                        $enrolledStartHour = strtotime($enrolledShedule->start_hour);
                        $enrolledEndHour = strtotime($enrolledShedule->end_hour);
                        if (($startHour >= $enrolledStartHour && $startHour <= $enrolledEndHour) || ($endHour >= $enrolledStartHour && $endHour <= $enrolledEndHour)) {
                            return ApiResponse::error(null, 'El grupo tiene cruce de horarios con otro grupo en el que ya está inscrito');
                        }
                    }
                }
            }

            $data = $request->validated();

            DB::beginTransaction();

            Enrollment::create($data);

            $enrollmentGroup = EnrollmentGroup::create([
                'student_id' => $request->studentId,
                'group_id' => $request->groupId,
                'period_id' => $enrollmentPeriod['periodId'],
                'status' => 'MATRICULADO',
                'created_by' => $user->id,
                'enrollment_modality' => 'PRESENCIAL',
                'special_enrollment' => false,
                'with_enrollment' => true,
            ]);

            foreach ($paymentsIds as $paymentId) {
                $payment = Payment::find($paymentId);
                $payment->enrollment_id = $enrollmentGroup->id;
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

            $user = Auth::user();

            $enrollmentPeriod = EnrollmentDeadline::activeEnrollmentPeriod();
            if (!$enrollmentPeriod)  ApiResponse::error(null, 'No se encontró el periodo de matrícula');

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

            $student = Student::select('student_type_id', 'id')
                ->where('id', $request->studentId)
                ->first();

            $group = Group::find($request->groupId);
            $shedules = Schedule::where('group_id', $group->id)->get();

            if ($shedules->count() == 0) {
                return ApiResponse::error(null, 'El grupo no tiene horarios asignados');
            }

            $enrolledSchedules = Schedule::select('schedules.id', 'schedules.start_hour', 'schedules.end_hour', 'schedules.day')
                ->join('groups', 'schedules.group_id', '=', 'groups.id')
                ->join('enrollment_groups', 'groups.id', '=', 'enrollment_groups.group_id')
                ->where('enrollment_groups.student_id', $student->id)
                ->where('enrollment_groups.period_id', $enrollmentPeriod['periodId'])
                ->where('enrollment_groups.status', 'MATRICULADO')
                ->get();

            //verificamos que no haya cruce de horarios
            foreach ($shedules as $shedule) {
                foreach ($enrolledSchedules as $enrolledShedule) {
                    if ($shedule->day == $enrolledShedule->day) {
                        $startHour = strtotime($shedule->start_hour);
                        $endHour = strtotime($shedule->end_hour);
                        $enrolledStartHour = strtotime($enrolledShedule->start_hour);
                        $enrolledEndHour = strtotime($enrolledShedule->end_hour);
                        if (($startHour >= $enrolledStartHour && $startHour <= $enrolledEndHour) || ($endHour >= $enrolledStartHour && $endHour <= $enrolledEndHour)) {
                            return ApiResponse::error(null, 'El grupo tiene cruce de horarios con otro grupo en el que ya está inscrito');
                        }
                    }
                }
            }

            $coursePrice = CoursePrice::select('presential_price', 'virtual_price')
                ->where('course_id', $group->course_id)
                ->where('student_type_id', $student->student_type_id)
                ->first();

            $price = $group->modality == 'PRESENCIAL' ? $coursePrice->presential_price : $coursePrice->virtual_price;

            if ($request->isSpecial) {
                $specialPrice = CoursePrice::select('presential_price', 'virtual_price')
                    ->join('courses', 'courses.id', '=', 'course_prices.course_id')
                    ->join('modules', 'modules.id', '=', 'courses.module_id')
                    ->where('course_prices.student_type_id', $student->student_type_id)
                    ->where('modules.is_extracurricular', true)
                    ->first();

                if (!$specialPrice) return ApiResponse::error(null, 'No se encontró el precio para el tipo de estudiante y el curso');

                $price = $group->modality == 'PRESENCIAL' ? $specialPrice->presential_price : $specialPrice->virtual_price;
            }

            $withEnrollment = false;

            if ($request->isSpecial == false) {
                $module = Module::select('modules.id as moduleId', 'modules.name as moduleName')
                    ->join('courses', 'courses.module_id', '=', 'modules.id')
                    ->where('courses.id', $group->course_id)
                    ->first();

                $lastEnrollment = EnrollmentGroup::select(
                    'periods.year',
                    'periods.month',
                )
                    ->join('groups', 'enrollment_groups.group_id', '=', 'groups.id')
                    ->join('courses', 'groups.course_id', '=', 'courses.id')
                    ->join('periods', 'enrollment_groups.period_id', '=', 'periods.id')
                    ->where('enrollment_groups.student_id', $request->studentId)
                    ->where('courses.module_id', $module->moduleId)
                    ->where('enrollment_groups.special_enrollment', false)
                    ->where('enrollment_groups.with_enrollment', true)
                    ->where('enrollment_groups.status', 'MATRICULADO')
                    ->orderBy('periods.id', 'desc')
                    ->first();

                if ($lastEnrollment) {
                    $lastEnrollmentDate = Carbon::createFromFormat('Y-m', $lastEnrollment->year . '-' . $lastEnrollment->month);
                    $currentDate = Carbon::now();
                    if ($lastEnrollmentDate->diffInMonths($currentDate) > 12) {

                        $modulePrice = ModulePrice::select('module_prices.price')
                            ->where('module_prices.module_id', $module->moduleId)
                            ->where('module_prices.student_type_id', $student->student_type_id)
                            ->first();

                        if (!$modulePrice) return ApiResponse::error(null, 'No se encontró el precio del módulo');

                        $enrollmentPrice = (float) $modulePrice->price;
                        $price += $enrollmentPrice;
                        $withEnrollment = true;
                    }
                } else {
                    $modulePrice = ModulePrice::select('module_prices.price')
                        ->where('module_prices.module_id', $module->moduleId)
                        ->where('module_prices.student_type_id', $student->student_type_id)
                        ->first();

                    if (!$modulePrice) return ApiResponse::error(null, 'No se encontró el precio del módulo');

                    $enrollmentPrice = (float) $modulePrice->price;
                    $price += $enrollmentPrice;
                    $withEnrollment = true;
                }
            }


            if ($totalPayment < $price) {
                return  ApiResponse::error(
                    [
                        'lastEnrollmentDate' => $lastEnrollmentDate ?? null,
                        'totalPayment' => number_format($totalPayment, 2),
                        'price' => number_format($price, 2),
                    ],
                    'El monto de los pagos no cubre el precio del grupo'
                );
            }

            DB::beginTransaction();

            if ($request->id) {
                $enrollmentGroup = EnrollmentGroup::find($request->id);
                $enrollmentGroup->status = 'MATRICULADO';
                $enrollmentGroup->save();
            } else {
                $enrollmentGroup = EnrollmentGroup::create([
                    'student_id' => $request->studentId,
                    'group_id' => $request->groupId,
                    'period_id' => $enrollmentPeriod['periodId'],
                    'status' => 'MATRICULADO',
                    'created_by' => $user->id,
                    'enrollment_modality' => 'PRESENCIAL',
                    'special_enrollment' => $request->isSpecial,
                    'with_enrollment' => $withEnrollment,
                ]);
            }

            foreach ($paymentsIds as $paymentId) {
                $payment = Payment::find($paymentId);
                $payment->enrollment_id = $enrollmentGroup->id;
                $payment->is_used = true;
                $payment->save();
            }

            DB::commit();
            return ApiResponse::success([
                'totalPayment' => number_format($totalPayment, 2),
                'price' => number_format($price, 2),
            ], 'Matriculado correctamente');
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al matricular el grupo');
        }
    }

    public function enrollmentGroupUpdate(Request $request)
    {
        try {

            $enrollmentPeriod = EnrollmentDeadline::activeEnrollmentPeriod();
            if (!$enrollmentPeriod) return ApiResponse::error(null, 'No se encontró el periodo de matrícula');

            $paymentsIds = array_map(function ($payment) {
                return Crypt::decrypt($payment);
            }, $request->payments);

            $enrollmentGroup = EnrollmentGroup::find($request->id);

            if (!$enrollmentGroup) return ApiResponse::error(null, 'No se encontró el registro');

            $totalNewPayment = Payment::where('student_id', $request->studentId)
                ->whereIn('id', array_unique($paymentsIds))
                ->sum('amount');

            $totalPayment = $totalNewPayment;

            $student = Student::select('student_type_id')
                ->where('id', $request->studentId)
                ->first();

            $group = Group::select('course_id', 'modality')
                ->where('id', $request->groupId)
                ->first();


            $newGroup = Group::find($request->groupId);
            $shedules = Schedule::where('group_id', $newGroup->id)->get();
            if ($shedules->count() == 0) {
                return ApiResponse::error(null, 'El grupo no tiene horarios asignados');
            }

            $enrolledSchedules = Schedule::select('schedules.id', 'schedules.start_hour', 'schedules.end_hour', 'schedules.day')
                ->join('groups', 'schedules.group_id', '=', 'groups.id')
                ->join('enrollment_groups', 'groups.id', '=', 'enrollment_groups.group_id')
                ->where('enrollment_groups.student_id', $student->id)
                ->where('enrollment_groups.period_id', $enrollmentPeriod['periodId'])
                ->where('enrollment_groups.group_id', '!=', $enrollmentGroup->group_id)
                ->where('enrollment_groups.status', 'MATRICULADO')
                ->get();

            foreach ($shedules as $shedule) {
                foreach ($enrolledSchedules as $enrolledShedule) {
                    if ($shedule->day == $enrolledShedule->day) {
                        $startHour = strtotime($shedule->start_hour);
                        $endHour = strtotime($shedule->end_hour);
                        $enrolledStartHour = strtotime($enrolledShedule->start_hour);
                        $enrolledEndHour = strtotime($enrolledShedule->end_hour);
                        if (($startHour >= $enrolledStartHour && $startHour <= $enrolledEndHour) || ($endHour >= $enrolledStartHour && $endHour <= $enrolledEndHour)) {
                            return ApiResponse::error(null, 'El grupo tiene cruce de horarios con otro grupo en el que ya está inscrito');
                        }
                    }
                }
            }

            $coursePrice = CoursePrice::select('presential_price', 'virtual_price')
                ->where('course_id', $group->course_id)
                ->where('student_type_id', $student->student_type_id)
                ->first();

            $price = $group->modality == 'PRESENCIAL' ? $coursePrice->presential_price : $coursePrice->virtual_price;

            if ($enrollmentGroup->with_enrollment) {
                $module = Module::select('modules.id as moduleId', 'modules.name as moduleName')
                    ->join('courses', 'courses.module_id', '=', 'modules.id')
                    ->where('courses.id', $group->course_id)
                    ->first();

                $modulePrice = ModulePrice::select('module_prices.price')
                    ->where('module_prices.module_id', $module->moduleId)
                    ->where('module_prices.student_type_id', $student->student_type_id)
                    ->first();

                if (!$modulePrice) return ApiResponse::error(null, 'No se encontró el precio del módulo');

                $enrollmentPrice = (float) $modulePrice->price;
                $price += $enrollmentPrice;
            } else if ($enrollmentGroup->special_enrollment && $request->isSpecial) {
                $specialPrice = CoursePrice::select('presential_price', 'virtual_price')
                    ->join('courses', 'courses.id', '=', 'course_prices.course_id')
                    ->join('modules', 'modules.id', '=', 'courses.module_id')
                    ->where('course_prices.student_type_id', $student->student_type_id)
                    ->where('modules.is_extracurricular', true)
                    ->first();

                if (!$specialPrice) return ApiResponse::error(null, 'No se encontró el precio para el tipo de estudiante y el curso');

                $price = $group->modality == 'PRESENCIAL' ? $specialPrice->presential_price : $specialPrice->virtual_price;
            } else {

                $module = Module::select('modules.id as moduleId', 'modules.name as moduleName')
                    ->join('courses', 'courses.module_id', '=', 'modules.id')
                    ->where('courses.id', $group->course_id)
                    ->first();

                $lastEnrollment = EnrollmentGroup::select('periods.year', 'periods.month')
                    ->join('groups', 'enrollment_groups.group_id', '=', 'groups.id')
                    ->join('courses', 'groups.course_id', '=', 'courses.id')
                    ->join('periods', 'enrollment_groups.period_id', '=', 'periods.id')
                    ->where('enrollment_groups.student_id', $request->studentId)
                    ->where('courses.module_id', $module->moduleId)
                    ->where('enrollment_groups.special_enrollment', false)
                    ->where('enrollment_groups.with_enrollment', true)
                    ->where('enrollment_groups.status', 'MATRICULADO')
                    ->orderBy('periods.id', 'desc')
                    ->first();

                if ($lastEnrollment) {
                    $lastEnrollmentDate = Carbon::createFromFormat('Y-m', $lastEnrollment->year . '-' . $lastEnrollment->month);
                    $currentDate = Carbon::now();
                    if ($lastEnrollmentDate->diffInMonths($currentDate) > 12) {

                        $modulePrice = ModulePrice::select('module_prices.price')
                            ->where('module_prices.module_id', $module->moduleId)
                            ->where('module_prices.student_type_id', $student->student_type_id)
                            ->first();

                        if (!$modulePrice) return ApiResponse::error(null, 'No se encontró el precio del módulo');

                        $enrollmentPrice = (float) $modulePrice->price;
                        $price += $enrollmentPrice;
                    }
                } else {
                    $modulePrice = ModulePrice::select('module_prices.price')
                        ->where('module_prices.module_id', $module->moduleId)
                        ->where('module_prices.student_type_id', $student->student_type_id)
                        ->first();

                    if (!$modulePrice) return ApiResponse::error(null, 'No se encontró el precio del módulo');

                    $enrollmentPrice = (float) $modulePrice->price;
                    $price += $enrollmentPrice;
                }
            }

            if ($totalPayment < $price) {
                return  ApiResponse::error(null, 'El monto de los pagos no cubre el precio del grupo');
            }

            DB::beginTransaction();

            $enrollmentGroup->group_id = $request->groupId;
            $enrollmentGroup->status = 'MATRICULADO';
            $enrollmentGroup->save();

            Payment::whereIn('id', array_unique($paymentsIds))
                ->where('student_id', $request->studentId)
                ->update([
                    'enrollment_id' => $enrollmentGroup->id,
                    'is_used' => true,
                ]);

            Payment::whereNotIn('id', array_unique($paymentsIds))
                ->where('student_id', $request->studentId)
                ->where('enrollment_id', $request->id)
                ->update([
                    'enrollment_id' => null,
                    'is_used' => false,
                ]);

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

            $enrollmentPeriod = EnrollmentDeadline::activeEnrollmentPeriod();
            if (!$enrollmentPeriod) return ApiResponse::error(null, 'No se encontró el periodo de matrícula');

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
                ->get();

            DB::beginTransaction();

            foreach ($payments as $payment) {
                $payment->enrollment_id = null;
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
                ->join('modules', 'enrollments.module_id', '=', 'modules.id')
                ->where('modules.curriculum_id', $request->curriculumId)
                ->where('enrollments.student_id', $request->studentId)
                ->orderBy('modules.is_extracurricular', 'asc')
                ->orderBy('modules.name', 'asc')
                ->get()->map(function ($enrollment) use ($request) {
                    $courses = Course::select(
                        'courses.id',
                        'courses.name',
                        'courses.code',
                        'areas.name as area',
                    )
                        ->leftJoin('areas', 'courses.area_id', '=', 'areas.id')
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
                                DB::raw('CONCAT(periods.year,"-",upper(months.name)) as period'),
                            )
                                ->leftJoin('enrollment_grades', 'enrollment_groups.id', '=', 'enrollment_grades.enrollment_group_id')
                                ->join('groups', 'enrollment_groups.group_id', '=', 'groups.id')
                                ->join('periods', 'groups.period_id', '=', 'periods.id')
                                ->join('months', 'periods.month', '=', 'months.id')
                                ->where('groups.course_id', $course->id)
                                ->where('enrollment_groups.special_enrollment', false)
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


            // if (!$request->courseId) {
            //     $course = Course::select('courses.id')
            //         ->join('groups', 'courses.id', '=', 'groups.course_id')
            //         ->where('groups.id', $request->groupId)
            //         ->first();
            //     if (!$course) return ApiResponse::error(null, 'Parámetros incorrectos, recargue la página e intente nuevamente');

            //     $request['courseId'] = $course->id;
            // }


            $enrollmentPeriod = EnrollmentDeadline::activeEnrollmentPeriod();

            if (!$enrollmentPeriod) return ApiResponse::error(null, 'No se encontró el periodo de matrícula');

            $requireEnrollmentPrice = false;

            $module = Module::select('modules.id as moduleId', 'modules.name as moduleName')
                ->join('courses', 'courses.module_id', '=', 'modules.id')
                ->where('courses.id', $request->courseId)
                ->first();

            if (!$request->isSpecial) {
                $lastEnrollment = EnrollmentGroup::select(
                    'periods.year',
                    'periods.month',
                )
                    ->join('groups', 'enrollment_groups.group_id', '=', 'groups.id')
                    ->join('courses', 'groups.course_id', '=', 'courses.id')
                    ->join('periods', 'enrollment_groups.period_id', '=', 'periods.id')
                    ->where('enrollment_groups.student_id', $request->studentId)
                    ->where('courses.module_id', $module->moduleId)
                    ->where('enrollment_groups.special_enrollment', false)
                    ->where('enrollment_groups.with_enrollment', true)
                    ->orderBy('periods.id', 'desc')
                    ->first();

                if ($lastEnrollment) {
                    $lastEnrollmentDate = Carbon::createFromFormat('Y-m', $lastEnrollment->year . '-' . $lastEnrollment->month);
                    $currentDate = Carbon::now();
                    if ($lastEnrollmentDate->diffInMonths($currentDate) > 12) {
                        $requireEnrollmentPrice = true;
                    }
                } else {
                    $requireEnrollmentPrice = true;
                }
            }

            $enrollmentGroups = Group::select(
                'groups.id',
                'groups.name as group',
                'groups.modality as modality',
                DB::raw('IF(groups.modality = "PRESENCIAL", course_prices.presential_price, course_prices.virtual_price) as price'),
                'laboratories.name as laboratory',
                DB::raw('CONCAT(teachers.name, " ", teachers.last_name_father, " ", teachers.last_name_mother) as teacher'),
                'max_students as maxStudents',
                'min_students as minStudents',
                'groups.status as status',
            )
                ->join('periods', 'groups.period_id', '=', 'periods.id')
                ->join('courses', 'groups.course_id', '=', 'courses.id')
                ->join('course_prices', 'course_prices.course_id', '=', 'courses.id')
                ->leftJoin('laboratories', 'groups.laboratory_id', '=', 'laboratories.id')
                ->leftJoin('teachers', 'groups.teacher_id', '=', 'teachers.id')
                ->where('course_prices.student_type_id', $student->student_type_id)
                ->where('courses.id', $request->courseId)
                ->where('periods.id', $enrollmentPeriod['periodId'])
                ->whereIn('groups.status', ['ABIERTO', 'CERRADO'])
                ->get()
                ->map(function ($group) use ($request, $student, $requireEnrollmentPrice, $module, $enrollmentPeriod) {
                    $enrolledStudents = EnrollmentGroup::where('enrollment_groups.group_id', $group->id)
                        ->where('status', 'MATRICULADO')
                        ->count();

                    $reservedStudents = EnrollmentGroup::join('groups', 'enrollment_groups.group_id', '=', 'groups.id')
                        ->where('groups.course_id', $request->courseId)
                        ->where('enrollment_groups.status', 'RESERVADO')
                        ->count();

                    if ($request->isSpecial) {
                        $specialPrice = CoursePrice::select('presential_price', 'virtual_price')
                            ->join('courses', 'courses.id', '=', 'course_prices.course_id')
                            ->join('modules', 'modules.id', '=', 'courses.module_id')
                            ->where('course_prices.student_type_id', $student->student_type_id)
                            ->where('modules.is_extracurricular', true)
                            ->first();

                        if (!$specialPrice) return ApiResponse::error(null, 'No se encontró el precio para el tipo de estudiante y el curso');

                        $price = $group->modality == 'PRESENCIAL' ? $specialPrice->presential_price : $specialPrice->virtual_price;
                        $group['price'] = number_format($price, 2);
                    } else {
                        $withEnrollment = EnrollmentGroup::select('with_enrollment')
                            ->join('groups', 'enrollment_groups.group_id', '=', 'groups.id')
                            ->where('enrollment_groups.student_id', $student->id)
                            ->where('enrollment_groups.period_id', $enrollmentPeriod['periodId'])
                            ->where('groups.course_id', $request->courseId)
                            ->where('enrollment_groups.status', 'MATRICULADO')
                            ->first();
                        if ($withEnrollment) {
                            if ($withEnrollment->with_enrollment) {
                                $requireEnrollmentPrice = true;
                            }
                        }
                    }

                    if ($requireEnrollmentPrice) {

                        $modulePrice = ModulePrice::select('module_prices.price')
                            ->where('module_prices.module_id', $module->moduleId)
                            ->where('module_prices.student_type_id', $student->student_type_id)
                            ->first();

                        if (!$modulePrice) return ApiResponse::error(null, 'No se encontró el precio del módulo');

                        $enrollmentPrice = (float) $modulePrice->price;

                        $group['price'] += $enrollmentPrice;
                        $group['price'] = number_format($group['price'], 2);
                    }

                    $group['enrolledStudents'] = $enrolledStudents;
                    $group['reservedStudents'] = $reservedStudents;
                    $group['schedules'] = Schedule::byGroup($group->id);

                    return $group;
                });

            return ApiResponse::success($enrollmentGroups, null);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), 'Error al cargar los registros');
        }
    }

    public function downloadEnrollmentPDF(Request $request)
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
            ->where('enrollment_groups.id', $request->id)
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
        return $pdf->output();
        // return ApiResponse::success($enrollment);
    }
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
                ->join('courses', function ($join) {
                    $join->on('modules.id', '=', 'courses.module_id')
                        ->where('courses.is_enabled', true);
                })
                ->where('modules.curriculum_id', $request->curriculumId)
                ->whereNotIn('modules.id', function ($query) use ($request) {
                    $query->select('enrollments.module_id')
                        ->from('enrollments')
                        ->where('enrollments.student_id', $request->studentId);
                })
                ->orderBy('modules.name', 'asc')
                ->get()->map(function ($enrollment) use ($request) {
                    $courses = Course::select(
                        'courses.id',
                        'courses.name',
                        'courses.code',
                        'areas.name as area',
                    )
                        ->leftJoin('areas', 'courses.area_id', '=', 'areas.id')
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
                                DB::raw('CONCAT(periods.year,"-",upper(months.name)) as period'),
                            )
                                ->leftJoin('enrollment_grades', 'enrollment_groups.id', '=', 'enrollment_grades.enrollment_group_id')
                                ->join('groups', 'enrollment_groups.group_id', '=', 'groups.id')
                                ->join('periods', 'groups.period_id', '=', 'periods.id')
                                ->join('months', 'periods.month', '=', 'months.id')
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
