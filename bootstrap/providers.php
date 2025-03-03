<?php

return [


    //UserServiceProvider
    App\Modules\User\Providers\UserServiceProvider::class,

    //AuthServiceProvider
    // App\Modules\Auth\Providers\AuthServiceProvider::class,

    App\Providers\AppServiceProvider::class,
    App\Modules\Period\Providers\PeriodServiceProvider::class,
    App\Modules\DocumentType\Providers\DocumentTypeServiceProvider::class,
    App\Modules\StudentType\Providers\StudentTypeServiceProvider::class,
    App\Modules\PaymentType\Providers\PaymentTypeServiceProvider::class,
    App\Modules\Person\Providers\PersonServiceProvider::class,
    App\Modules\Student\Providers\StudentServiceProvider::class,
    App\Modules\Teacher\Providers\TeacherServiceProvider::class,
    App\Modules\Area\Providers\AreaServiceProvider::class,
    App\Modules\Module\Providers\ModuleServiceProvider::class,
    App\Modules\Course\Providers\CourseServiceProvider::class,
    App\Modules\Laboratory\Providers\LaboratoryServiceProvider::class,
    App\Modules\Curriculum\Providers\CurriculumServiceProvider::class,
    App\Modules\CurriculumCourse\Providers\CurriculumCourseServiceProvider::class,
    App\Modules\Group\Providers\GroupServiceProvider::class,
    App\Modules\Schedule\Providers\ScheduleServiceProvider::class,

    // PriceServiceProvider
    App\Modules\ModulePrice\Providers\ModulePriceServiceProvider::class,
    App\Modules\CoursePrice\Providers\CoursePriceServiceProvider::class,
    //PaymentServiceProvider
    App\Modules\Payment\Providers\PaymentServiceProvider::class,
    //EnrollmentServiceProvider
    App\Modules\Enrollment\Providers\EnrollmentServiceProvider::class,
    //PreEnrollmentServiceProvider
    // App\Modules\PreEnrollment\Providers\PreEnrollmentServiceProvider::class,
    //EnrollmentGroupServiceProvider
    App\Modules\EnrollmentGroup\Providers\EnrollmentGroupServiceProvider::class,
    //EnrollmentGradeServiceProvider
    App\Modules\EnrollmentGrade\Providers\EnrollmentGradeServiceProvider::class,
    //RoleServiceProvider
    App\Modules\Role\Providers\RoleServiceProvider::class,

];
