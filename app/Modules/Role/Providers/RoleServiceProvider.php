<?php

namespace App\Modules\Role\Providers;

use Illuminate\Support\ServiceProvider;

class RoleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
    }

    public function register()
    {
        //
    }
}
