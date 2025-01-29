<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateModuleCommand extends Command
{
    protected $signature = 'create:module {name}';
    protected $description = 'Crea un nuevo módulo API con la estructura básica';

    public function handle()
    {
        // Obtener el nombre del módulo en PascalCase y snake_case
        $moduleName = ucfirst($this->argument('name'));
        // to NombreModulo and nombre-modulo
        $moduleNameLower = Str::kebab($moduleName);

        // Ruta base de los módulos
        $modulePath = app_path("Modules/{$moduleName}");

        if (File::exists($modulePath)) {
            $this->error("El módulo '{$moduleName}' ya existe.");
            return;
        }

        // Crear la estructura del módulo
        File::makeDirectory($modulePath, 0755, true);
        File::makeDirectory("{$modulePath}/Http/Controllers", 0755, true);
        File::makeDirectory("{$modulePath}/Http/Requests", 0755, true);
        File::makeDirectory("{$modulePath}/Http/Resources", 0755, true);
        File::makeDirectory("{$modulePath}/Models", 0755, true);
        File::makeDirectory("{$modulePath}/Routes", 0755, true);
        File::makeDirectory("{$modulePath}/Providers", 0755, true);
        File::makeDirectory("{$modulePath}/Database/Migrations", 0755, true);

        // Crear archivos base
        $this->createServiceProvider($moduleName, $modulePath);
        $this->createRoutesFile($moduleName, $moduleNameLower, $modulePath);
        $this->createController($moduleName, $modulePath);
        $this->createModel($moduleName, $modulePath);

        $this->info("Módulo '{$moduleName}' creado con éxito.");
    }

    private function createServiceProvider($moduleName, $modulePath)
    {
        $providerTemplate = <<<PHP
<?php

namespace App\Modules\\{$moduleName}\Providers;

use Illuminate\Support\ServiceProvider;

class {$moduleName}ServiceProvider extends ServiceProvider
{
    public function boot()
    {
        \$this->loadRoutesFrom(__DIR__ . '/../Routes/api.php');
        \$this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    public function register()
    {
        //
    }
}
PHP;

        File::put("{$modulePath}/Providers/{$moduleName}ServiceProvider.php", $providerTemplate);
    }

    private function createRoutesFile($moduleName, $moduleNameLower, $modulePath)
    {
        $routesTemplate = <<<PHP
<?php

use Illuminate\Support\Facades\Route;
use App\Modules\\{$moduleName}\Http\Controllers\\{$moduleName}Controller;

Route::prefix('api/{$moduleNameLower}')->group(function () {
    Route::get('/', [{$moduleName}Controller::class, 'index']);
    Route::post('/', [{$moduleName}Controller::class, 'store']);
    Route::get('/{id}', [{$moduleName}Controller::class, 'show']);
    Route::put('/{id}', [{$moduleName}Controller::class, 'update']);
    Route::delete('/{id}', [{$moduleName}Controller::class, 'destroy']);
});
PHP;

        File::put("{$modulePath}/Routes/api.php", $routesTemplate);
    }

    private function createController($moduleName, $modulePath)
    {
        $controllerTemplate = <<<PHP
<?php

namespace App\Modules\\{$moduleName}\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class {$moduleName}Controller extends Controller
{
    public function index()
    {
        return response()->json(['message' => '{$moduleName} index']);
    }

    public function store(Request \$request)
    {
        return response()->json(['message' => '{$moduleName} store']);
    }

    public function show(\$id)
    {
        return response()->json(['message' => '{$moduleName} show', 'id' => \$id]);
    }

    public function update(Request \$request, \$id)
    {
        return response()->json(['message' => '{$moduleName} update', 'id' => \$id]);
    }

    public function destroy(\$id)
    {
        return response()->json(['message' => '{$moduleName} destroy', 'id' => \$id]);
    }
}
PHP;

        File::put("{$modulePath}/Http/Controllers/{$moduleName}Controller.php", $controllerTemplate);
    }

    private function createModel($moduleName, $modulePath)
    {
        $modelTemplate = <<<PHP
<?php

namespace App\Modules\\{$moduleName}\Models;

use Illuminate\Database\Eloquent\Model;

class {$moduleName} extends Model
{
    protected \$fillable = [];
}
PHP;

        File::put("{$modulePath}/Models/{$moduleName}.php", $modelTemplate);
    }
}
