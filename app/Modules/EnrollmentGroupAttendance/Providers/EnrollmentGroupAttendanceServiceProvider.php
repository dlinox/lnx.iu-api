<?php

namespace App\Modules\EnrollmentGroupAttendance\Providers;

use Illuminate\Support\ServiceProvider;

class EnrollmentGroupAttendanceServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    public function register()
    {
        //
    }
}
