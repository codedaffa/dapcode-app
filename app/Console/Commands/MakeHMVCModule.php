<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeHMVCModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:hmvc {name : The name of the HMVC module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new HMVC module with its BaseController placed in app/Http/Controllers/Core/{Name}Controllers.php';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $viewKey = strtolower($name);
        $modulePath = app_path("Modules/{$name}");
        $coreControllerPath = app_path('Http/Controllers/Core');

        if (File::isDirectory($modulePath)) {
            $this->error("HMVC Module [{$name}] already exists!");
            return 1;
        }

        // 1. Ensure Core folder in Controllers exists
        if (!File::isDirectory($coreControllerPath)) {
            File::makeDirectory($coreControllerPath, 0755, true);
        }

        // 2. Create Module BaseController in app/Http/Controllers/Core/{Name}Controllers.php
        $coreBaseControllerFile = "{$coreControllerPath}/{$name}Controllers.php";
        if (!File::exists($coreBaseControllerFile)) {
            $coreStub = <<<PHP
<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;

class {$name}Controllers extends Controller
{
    /**
     * Module name identifier.
     *
     * @var string
     */
    protected \$moduleName = '{$name}';

    /**
     * Helper to render view within {$name} module namespace.
     *
     * @param string \$view
     * @param array \$data
     * @param bool \$return
     * @return \Illuminate\Contracts\View\View|string
     */
    protected function moduleRender(string \$view, array \$data = [], bool \$return = false)
    {
        \$viewPath = strpos(\$view, '::') === false ? "{$viewKey}::{\$view}" : \$view;
        return \$this->render(\$viewPath, array_merge(['moduleName' => \$this->moduleName], \$data), \$return);
    }
}
PHP;
            File::put($coreBaseControllerFile, $coreStub);
        }

        // 3. Create module directories
        File::makeDirectory("{$modulePath}/Controllers", 0755, true);
        File::makeDirectory("{$modulePath}/Models", 0755, true);
        File::makeDirectory("{$modulePath}/Views", 0755, true);
        File::makeDirectory("{$modulePath}/Database/Migrations", 0755, true);

        // 4. Generate Main Controller extending Core\{Name}Controllers
        $controllerStub = <<<PHP
<?php

namespace App\Modules\\{$name}\\Controllers;

use App\Http\Controllers\Core\\{$name}Controllers;
use Illuminate\Http\Request;

class {$name} extends {$name}Controllers
{
    /**
     * Display the index page for {$name} module.
     * Accessible via: /{$name}
     *
     * @param Request \$request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request \$request)
    {
        \$moduleKey = '{$viewKey}';
        return \$this->moduleRender('index', [
            'title' => __("modules.{\$moduleKey}.title") !== "modules.{\$moduleKey}.title" ? __("modules.{\$moduleKey}.title") : "{$name} Module",
            'subtitle' => __("modules.{\$moduleKey}.subtitle") !== "modules.{\$moduleKey}.subtitle" ? __("modules.{\$moduleKey}.subtitle") : "HMVC {$name} Module",
        ]);
    }
}
PHP;
        File::put("{$modulePath}/Controllers/{$name}.php", $controllerStub);

        // 5. Generate Main Model extending Illuminate\Database\Eloquent\Model directly
        $modelStub = <<<PHP
<?php

namespace App\Modules\\{$name}\\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$name} extends Model
{
    use HasFactory;

    protected \$guarded = [];
}
PHP;
        File::put("{$modulePath}/Models/{$name}.php", $modelStub);

        // 6. Generate View
        $viewStub = <<<BLADE
<div class="content-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
        <div style="min-width: 0; flex: 1;">
            <h2 style="font-size: 20px; font-weight: 700; color: #fff; margin-bottom: 4px;">{{ \$title }}</h2>
            <p style="color: var(--text-muted); font-size: 13.5px;">Modul <strong>{$name}</strong> berhasil dibuat dan mewarisi <code>App\Http\Controllers\Core\\{$name}Controllers</code>.</p>
        </div>
    </div>

    <div class="stat-item">
        <div style="font-family: monospace; font-size: 13px; color: #38bdf8; word-break: break-all;">
            <i class="fa-solid fa-folder-tree"></i> Controller: app/Modules/{$name}/Controllers/{$name}.php<br>
            <i class="fa-solid fa-cube"></i> Base Core: app/Http/Controllers/Core/{$name}Controllers.php
        </div>
    </div>
</div>
BLADE;
        File::put("{$modulePath}/Views/index.blade.php", $viewStub);

        $this->info("HMVC Module [{$name}] created successfully!");
        $this->line("<comment>Base Core Controller:</comment> <info>app/Http/Controllers/Core/{$name}Controllers.php</info>");
        $this->line("<comment>Module Controller:</comment>    <info>app/Modules/{$name}/Controllers/{$name}.php</info>");
        $this->line("<comment>Module Model:</comment>         <info>app/Modules/{$name}/Models/{$name}.php</info>");
        $this->line("<comment>Access URL:</comment>           <info>/" . Str::kebab($name) . "</info> or <info>/{$name}</info>");

        return 0;
    }
}
