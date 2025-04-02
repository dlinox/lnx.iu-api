<?php

return [
    App\Providers\AppServiceProvider::class,

    App\Modules\Auth\Providers\AuthServiceProvider::class,
    App\Modules\Role\Providers\RoleServiceProvider::class,
    App\Modules\User\Providers\UserServiceProvider::class,


    //Period 
    App\Modules\Period\Providers\PeriodServiceProvider::class,
    App\Modules\DocumentType\Providers\DocumentTypeServiceProvider::class,
    App\Modules\StudentType\Providers\StudentTypeServiceProvider::class,
    App\Modules\PaymentType\Providers\PaymentTypeServiceProvider::class,

    
    App\Modules\Laboratory\Providers\LaboratoryServiceProvider::class,
    App\Modules\Curriculum\Providers\CurriculumServiceProvider::class,
    App\Modules\Area\Providers\AreaServiceProvider::class,
    App\Modules\Module\Providers\ModuleServiceProvider::class,
    App\Modules\Course\Providers\CourseServiceProvider::class,
    App\Modules\ModulePrice\Providers\ModulePriceServiceProvider::class,
    App\Modules\CoursePrice\Providers\CoursePriceServiceProvider::class,

    App\Modules\Person\Providers\PersonServiceProvider::class,
    App\Modules\Teacher\Providers\TeacherServiceProvider::class,
    App\Modules\Group\Providers\GroupServiceProvider::class,
    App\Modules\Schedule\Providers\ScheduleServiceProvider::class,
    App\Modules\Student\Providers\StudentServiceProvider::class,
    App\Modules\Payment\Providers\PaymentServiceProvider::class,
    App\Modules\Enrollment\Providers\EnrollmentServiceProvider::class,
    App\Modules\EnrollmentGroup\Providers\EnrollmentGroupServiceProvider::class,
    App\Modules\EnrollmentGrade\Providers\EnrollmentGradeServiceProvider::class,
    App\Modules\EnrollmentUnitGrade\Providers\EnrollmentUnitGradeServiceProvider::class,
    App\Modules\EnrollmentDeadline\Providers\EnrollmentDeadlineServiceProvider::class,

    // PreRegisterServiceProvider::class,
    App\Modules\PreRegister\Providers\PreRegisterServiceProvider::class,
    
];
