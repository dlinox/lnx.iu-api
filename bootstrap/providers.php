<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Modules\Schedule\Providers\ScheduleServiceProvider::class,
    App\Modules\SessionTime\Providers\SessionTimeServiceProvider::class,
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
    App\Modules\CurriculumModule\Providers\CurriculumModuleServiceProvider::class,
    App\Modules\CurriculumModuleCourse\Providers\CurriculumModuleCourseServiceProvider::class,
];
