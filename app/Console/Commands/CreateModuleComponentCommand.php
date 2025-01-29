<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateModuleComponentCommand extends Command
{
    protected $signature = 'create:module-component {module} {type} {name}';
    protected $description = 'Crea un componente específico dentro de un módulo (model, migration, resource, etc.)';

    public function handle()
    {
        $moduleName = ucfirst($this->argument('module'));
        $type = strtolower($this->argument('type'));
        $name = ucfirst($this->argument('name'));

        $modulePath = app_path("Modules/{$moduleName}");

        // Verificar que el módulo exista
        if (!File::exists($modulePath)) {
            $this->error("El módulo '{$moduleName}' no existe. Usa 'create:module' para crearlo primero.");
            return;
        }

        switch ($type) {
            case 'model':
                $this->createModel($modulePath, $name);
                break;
            case 'migration':
                $this->createMigration($moduleName, $name);
                break;
            case 'controller':
                $this->createController($modulePath, $name);
                break;
            case 'resource':
                $this->createResource($modulePath, $name);
                break;
            case 'request':
                $this->createRequest($modulePath, $name);
                break;
            default:
                $this->error("Tipo '{$type}' no reconocido. Usa 'model', 'migration', 'controller', 'resource' o 'request'.");
        }
    }

    protected function createModel($modulePath, $name)
    {
        $filePath = "{$modulePath}/Models/{$name}.php";
        if (File::exists($filePath)) {
            $this->error("El modelo '{$name}' ya existe.");
            return;
        }

        $namespace = "App\\Modules\\" . basename($modulePath) . "\\Models";

        $content = <<<PHP
<?php

namespace {$namespace};

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$name} extends Model
{
    use HasFactory;

    protected \$fillable = [];
}
PHP;

        File::put($filePath, $content);
        $this->info("Modelo '{$name}' creado exitosamente.");
    }

    protected function createMigration($moduleName, $name)
    {
        $tableName = Str::snake(Str::pluralStudly($name));
        $timestamp = date('Y_m_d_His');
        $fileName = "{$timestamp}_create_{$tableName}_table.php";

        $modulePath = base_path("database/migrations/modules/{$moduleName}");
        File::ensureDirectoryExists($modulePath);

        $filePath = "{$modulePath}/{$fileName}";

        $content = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            \$table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('{$tableName}');
    }
};
PHP;

        File::put($filePath, $content);
        $this->info("Migración '{$fileName}' creada exitosamente en '{$modulePath}'.");
    }

    protected function createController($modulePath, $name)
    {
        $filePath = "{$modulePath}/Http/Controllers/{$name}Controller.php";
        if (File::exists($filePath)) {
            $this->error("El controlador '{$name}Controller' ya existe.");
            return;
        }

        $namespace = "App\\Modules\\" . basename($modulePath) . "\\Http\\Controllers";

        $content = <<<PHP
<?php

namespace {$namespace};

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class {$name}Controller extends Controller
{
    public function index()
    {
        //
    }
}
PHP;

        File::put($filePath, $content);
        $this->info("Controlador '{$name}Controller' creado exitosamente.");
    }

    protected function createResource($modulePath, $name)
    {
        $filePath = "{$modulePath}/Http/Resources/{$name}Resource.php";
        if (File::exists($filePath)) {
            $this->error("El recurso '{$name}Resource' ya existe.");
            return;
        }

        $namespace = "App\\Modules\\" . basename($modulePath) . "\\Http\\Resources";

        $content = <<<PHP
<?php

namespace {$namespace};

use Illuminate\Http\Resources\Json\JsonResource;

class {$name}Resource extends JsonResource
{
    public function toArray(\$request)
    {
        return parent::toArray(\$request);
    }
}
PHP;

        File::put($filePath, $content);
        $this->info("Recurso '{$name}Resource' creado exitosamente.");
    }

    protected function createRequest($modulePath, $name)
    {
        $filePath = "{$modulePath}/Http/Requests/{$name}Request.php";
        if (File::exists($filePath)) {
            $this->error("El request '{$name}Request' ya existe.");
            return;
        }

        $namespace = "App\\Modules\\" . basename($modulePath) . "\\Http\\Requests";

        $content = <<<PHP
<?php

namespace {$namespace};

use Illuminate\Foundation\Http\FormRequest;

class {$name}Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            //
        ];
    }
}
PHP;

        File::put($filePath, $content);
        $this->info("Request '{$name}Request' creado exitosamente.");
    }
}
