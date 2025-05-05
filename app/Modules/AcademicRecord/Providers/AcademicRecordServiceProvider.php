<?php

namespace App\Modules\AcademicRecord\Providers;

use Illuminate\Support\ServiceProvider;

class AcademicRecordServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    public function register() {}
}
