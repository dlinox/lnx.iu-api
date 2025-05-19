<?php

namespace App\Modules\Report\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Month;
use App\Modules\Curriculum\Models\Curriculum;
use App\Modules\EnrollmentDeadline\Models\EnrollmentDeadline;
use App\Modules\EnrollmentGroup\Models\EnrollmentGroup;
use App\Modules\Group\Models\Group;
use App\Modules\Module\Models\Module;
use App\Modules\Period\Models\Period;
use App\Modules\Schedule\Models\Schedule;
use App\Modules\Student\Models\Student;
use App\Modules\Teacher\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{

    public function studentEnrollements(Request $request)
    {
        $mpdf = new \Mpdf\Mpdf(
            [
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 32,
                'margin_bottom' => 10,
                'margin_header' => 5,
                'margin_footer' => 5,
            ]
        );

        $student = Student::selectRaw(
            'CONCAT_WS(" ", students.last_name_father, students.last_name_mother) AS lastName,
            students.name AS name,
            students.code AS code,
            student_types.name AS type'
        )
            ->join('student_types', 'student_types.id', '=', 'students.student_type_id')
            ->where('students.id', $request->id)
            ->first();

        $enrollments = EnrollmentGroup::selectRaw(
            'CONCAT(periods.`year`,"-",  months.short_name) AS period,
            `groups`.`name` AS `group`, 
            courses.`name` AS course, 
            enrollment_groups.enrollment_modality AS enrollmentModality,
            enrollment_groups.`status` AS enrollmentStatus,
            `groups`.`status` AS groupStatus'
        )
            ->leftJoin('groups', 'groups.id', '=', 'enrollment_groups.group_id')
            ->leftJoin('courses', 'courses.id', '=', 'groups.course_id')
            ->leftJoin('periods', 'periods.id', '=', 'enrollment_groups.period_id')
            ->join('months', 'months.id', '=', 'periods.month')
            ->where('student_id', $request->id)
            ->get();

        $htmlContent = view('pdf.Report.Student.Enrollments', compact('enrollments', 'student'))->render();
        $htmlHeader = view('pdf.Report.Student._header')->render();
        $htmlFooter = view('pdf.Report.Student._footer')->render();

        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->SetTitle('Matriculas del Estudiante');

        $mpdf->WriteHTML($htmlContent);

        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf');
    }

    public function studentGrades(Request $request)
    {
        $mpdf = new \Mpdf\Mpdf(
            [
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 32,
                'margin_bottom' => 10,
                'margin_header' => 5,
                'margin_footer' => 5,
            ]
        );

        $student = Student::selectRaw(
            'CONCAT_WS(" ", students.last_name_father, students.last_name_mother) AS lastName,
            students.name AS name,
            students.code AS code,
            student_types.name AS type'
        )
            ->join('student_types', 'student_types.id', '=', 'students.student_type_id')
            ->where('students.id', $request->id)
            ->first();

        $data = EnrollmentGroup::select(
            'curriculums.id',
            'curriculums.name'
        )
            ->distinct()
            ->join('groups', 'groups.id', '=', 'enrollment_groups.group_id')
            ->join('courses', 'courses.id', '=', 'groups.course_id')
            ->join('curriculums', 'curriculums.id', '=', 'courses.curriculum_id')
            ->where('enrollment_groups.student_id', $request->id)
            ->get()->map(function ($curriculum) use ($request) {

                $modules = EnrollmentGroup::select(
                    'modules.id',
                    'modules.name',
                    'modules.code',
                    DB::raw('IF(modules.is_extracurricular = 1, "SI", "NO") as isExtracurricular'),
                )
                    ->distinct()
                    ->join('groups', 'groups.id', '=', 'enrollment_groups.group_id')
                    ->join('courses', 'courses.id', '=', 'groups.course_id')
                    ->join('curriculums', 'curriculums.id', '=', 'courses.curriculum_id')
                    ->join('modules', 'modules.id', '=', 'courses.module_id')
                    ->where('enrollment_groups.student_id', $request->id)
                    ->where('curriculums.id', $curriculum->id)
                    ->get();

                $curriculum->modules = $modules->map(function ($module) use ($request) {
                    $grades = EnrollmentGroup::selectRaw(
                        'CONCAT(periods.`year`,"-",  months.short_name) AS period,
                        `groups`.`name` AS `group`, 
                        courses.`name` AS course, 
                        enrollment_groups.enrollment_modality AS enrollmentModality,
                        enrollment_groups.`status` AS enrollmentStatus,
                        `groups`.`status` AS groupStatus,
                        IF(enrollment_grades.grade IS NULL, "--", enrollment_grades.grade) AS grade'
                    )
                        ->join('groups', 'groups.id', '=', 'enrollment_groups.group_id')
                        ->join('courses', 'courses.id', '=', 'groups.course_id')
                        ->join('periods', 'periods.id', '=', 'enrollment_groups.period_id')
                        ->join('months', 'months.id', '=', 'periods.month')
                        ->leftJoin('enrollment_grades', 'enrollment_grades.enrollment_group_id', '=', 'enrollment_groups.id')
                        ->where('student_id', $request->id)
                        ->where('courses.module_id', $module->id)
                        ->get();

                    $module->grades = $grades;

                    return $module;
                });
                return $curriculum;
            });

        $htmlContent = view('pdf.Report.Student.Grades', compact('student', 'data'))->render();
        $htmlHeader = view('pdf.Report.Student._header')->render();
        $htmlFooter = view('pdf.Report.Student._footer')->render();

        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->SetTitle('Acta de Notas');

        $mpdf->WriteHTML($htmlContent);

        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf');
    }

    public function groupEnrolledStudents(Request $request)
    {

        $mpdf = new \Mpdf\Mpdf(
            [
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 32,
                'margin_bottom' => 10,
                'margin_header' => 5,
                'margin_footer' => 5,
            ]
        );

        $group = Group::select(
            'groups.id',
            'groups.name AS group',
            'courses.name AS course',
            DB::raw('CONCAT(months.short_name, ". del " , periods.`year`) AS period'),
            DB::raw('CONCAT_WS(" ",teachers.last_name_father, teachers.last_name_mother) AS teacherLastName'),
            'teachers.name AS teacherName',
        )
            ->join('courses', 'courses.id', 'groups.course_id')
            ->join('periods', 'periods.id', 'groups.period_id')
            ->join('months', 'months.id', 'periods.month')
            ->leftJoin('teachers', 'teachers.id', 'groups.teacher_id')
            ->where('groups.id', $request->groupId)
            ->first();

        $group['schedule'] = Schedule::byGroup($request->groupId);

        $students = EnrollmentGroup::selectRaw(
            'CONCAT_WS(" ", students.last_name_father, students.last_name_mother) AS lastName,
            students.name AS name,
            students.code AS code,
            student_types.name AS type,
            students.phone AS phone,
            enrollment_groups.enrollment_modality AS enrollmentModality'
        )
            ->join('students', 'students.id', '=', 'enrollment_groups.student_id')
            ->join('student_types', 'student_types.id', '=', 'students.student_type_id')
            ->where('enrollment_groups.group_id', $request->groupId)
            ->get();


        $htmlContent = view('pdf.Report.Group.EnrolledStudents', compact('students', 'group'))->render();
        $htmlHeader = view('pdf.Report.Group._header')->render();
        $htmlFooter = view('pdf.Report.Group._footer')->render();

        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->SetTitle('Lista de Estudiantes Matriculados');

        $mpdf->WriteHTML($htmlContent);

        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf');
    }

    public function enabledGroups(Request $request)
    {
        $mpdf = new \Mpdf\Mpdf(
            [
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 32,
                'margin_bottom' => 10,
                'margin_header' => 5,
                'margin_footer' => 5,
            ]
        );

        $period = DB::table('periods')
            ->selectRaw(
                'CONCAT(months.short_name, ". del " , periods.`year`) AS period,
                periods.id AS id'
            )
            ->join('months', 'months.id', 'periods.month')
            ->where('periods.id', $request->periodId)
            ->first();


        $data = Curriculum::select(
            'curriculums.id',
            'curriculums.name'
        )
            ->distinct()
            ->join('courses', 'courses.curriculum_id', '=', 'curriculums.id')
            ->join('groups', 'groups.course_id', '=', 'courses.id')
            ->where('groups.period_id', $request->periodId)
            ->orderBy('curriculums.id')
            ->get()->map(function ($curriculum) use ($request) {

                $modules = Module::select(
                    'modules.id',
                    'modules.level',
                    'modules.name',
                    'modules.code',
                    DB::raw('IF(modules.is_extracurricular = 1, "SI", "NO") as isExtracurricular'),
                )
                    ->distinct()
                    ->join('courses', 'courses.module_id', '=', 'modules.id')
                    ->join('groups', 'groups.course_id', '=', 'courses.id')
                    ->where('groups.period_id', $request->periodId)
                    ->where('courses.curriculum_id', $curriculum->id)
                    ->orderBy('modules.level')
                    ->get()->map(function ($module) use ($request) {
                        $groups = Group::select(
                            'groups.id',
                            'groups.name AS group',
                            'courses.name AS course',
                            DB::raw('CONCAT_WS(" ",teachers.last_name_father, teachers.last_name_mother) AS teacherLastName'),
                            'teachers.name AS teacherName',
                        )
                            ->join('courses', 'courses.id', 'groups.course_id')
                            ->leftJoin('teachers', 'teachers.id', 'groups.teacher_id')
                            ->where('groups.period_id', $request->periodId)
                            ->where('courses.module_id', $module->id)
                            ->orderBy('courses.order')
                            ->get()->map(function ($group) use ($request) {
                                $group['schedule'] = Schedule::byGroup($group->id);
                                $group['students'] = EnrollmentGroup::where('group_id', $group->id)->count();
                                return $group;
                            });
                        $module->groups = $groups ? $groups : [];
                        return $module;
                    });
                $curriculum->modules = $modules ? $modules : [];
                return $curriculum;
            });

        $data = $data ? $data : [];

        $htmlContent = view('pdf.Report.Group.EnabledGroups', compact('period', 'data'))->render();
        $htmlHeader = view('pdf.Report.Group._header')->render();
        $htmlFooter = view('pdf.Report.Group._footer')->render();

        $mpdf->SetHTMLHeader($htmlHeader);
        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->SetTitle('Grupos Habilitados');

        $mpdf->WriteHTML($htmlContent);

        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf');
    }

    public function collectionGroup(Request $request)
    {
        $mpdf = new \Mpdf\Mpdf(
            [
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 32,
                'margin_bottom' => 10,
                'margin_header' => 5,
                'margin_footer' => 5,
            ]
        );

        $period = DB::table('periods')
            ->selectRaw(
                'CONCAT(months.short_name, ". del " , periods.`year`) AS period,
                periods.id AS id'
            )
            ->join('months', 'months.id', 'periods.month')
            ->where('periods.id', $request->periodId)
            ->first();


        $groups = Group::select(
            'groups.id',
            'groups.name AS group',
            'courses.module_id',
            'courses.name AS course',
            DB::raw('SUM(IFNULL(payments.amount,0)) AS amount')
        )
            ->join('courses', 'courses.id', 'groups.course_id')
            ->leftJoin('enrollment_groups', 'enrollment_groups.group_id', 'groups.id')
            ->leftJoin('payments', 'payments.enrollment_id', 'enrollment_groups.id')
            ->where('groups.period_id', $request->periodId)
            ->groupBy('groups.id')
            ->orderBy('courses.module_id')
            ->orderBy('courses.name')
            ->get();

        $total = $groups->sum('amount');
        $total = number_format($total, 2, '.', '');


        $htmlContent = view('pdf.Report.Group.CollectionGroups', compact('period', 'groups', 'total'))->render();
        $htmlHeader = view('pdf.Report.Group._header')->render();
        $htmlFooter = view('pdf.Report.Group._footer')->render();
        $mpdf->SetHTMLHeader($htmlHeader);

        $mpdf->SetHTMLFooter($htmlFooter);
        $mpdf->SetTitle('Recaudación por Grupo');
        $mpdf->WriteHTML($htmlContent);
        return response($mpdf->Output('', 'S'), 200)
            ->header('Content-Type', 'application/pdf');
    }

    public function dashboardEnrollments()
    {
        $enrollments = 0;
        $period = null;
        $period = EnrollmentDeadline::activeEnrollmentPeriod();

        if ($period) {
            $enrollments = EnrollmentGroup::where('period_id', $period['periodId'])->count();
        }
        $data = [
            'enrollments' => $enrollments,
            'period' => $period ? $period['period'] : null,
        ];
        return ApiResponse::success($data);
    }

    public function dashboardAllStudents()
    {
        $students = Student::count();

        return ApiResponse::success($students);
    }

    public function dashboardAllTeachers()
    {
        $teachers = Teacher::count();
        return ApiResponse::success($teachers);
    }

    public function dashboardAcademic()
    {

        $data = [
            'curriculums' => Curriculum::count(),
            'modules' => Module::count(),
            'areas' => DB::table('areas')->count(),
            'courses' => DB::table('courses')->count(),
        ];

        return ApiResponse::success($data);
    }

    //get studentEnrollmentsForMonth
    public function getEnrollmentsByYear(Request $request)
    {

        $enrollmentsMonth = Month::selectRaw('
                months.id AS monthId,
                months.name AS month,
                COUNT(enrollment_groups.id) AS enrollments
            ')
            ->leftJoin('periods', function ($join) use ($request) {
                $join->on('periods.month', '=', 'months.id')
                    ->where('periods.year', '=', $request->year);
            })
            ->leftJoin('enrollment_groups', 'enrollment_groups.period_id', '=', 'periods.id')
            ->groupBy('months.id', 'months.name')
            ->orderBy('months.id')
            ->get()->pluck('enrollments')->toArray();

        return ApiResponse::success($enrollmentsMonth);
    }

    public function getEnabledGroupsByYear(Request $request)
    {
        $enrollmentsMonth = Month::selectRaw('
                months.id AS monthId,
                months.name AS month,
                COUNT(groups.id) AS enrollments
            ')
            ->leftJoin('periods', function ($join) use ($request) {
                $join->on('periods.month', '=', 'months.id')
                    ->where('periods.year', '=', $request->year);
            })
            ->leftJoin('groups', 'groups.period_id', '=', 'periods.id')
            ->groupBy('months.id', 'months.name')
            ->orderBy('months.id')
            ->get()->pluck('enrollments')->toArray();

        return ApiResponse::success($enrollmentsMonth);
    }

    public function allYearsForSelect()
    {
        $years = Period::select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->get()
            ->map(function ($year) {
                return [
                    'label' => $year->year,
                    'value' => $year->year,
                ];
            });


        return ApiResponse::success($years);
    }
}
