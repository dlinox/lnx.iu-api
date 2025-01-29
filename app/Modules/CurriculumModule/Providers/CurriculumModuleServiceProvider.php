<?php

namespace App\Modules\CurriculumModule\Providers;

use Illuminate\Support\ServiceProvider;

class CurriculumModuleServiceProvider extends ServiceProvider
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