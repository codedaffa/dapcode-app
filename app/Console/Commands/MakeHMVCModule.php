<?php

namespace App\Console\Commands;

use App\Services\Dapcode\InstallationService;
use App\Services\Dapcode\IntegrityService;
use App\Services\Dapcode\ModuleEncryptionService;
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
    protected $signature = 'make:module {name : The name of the new module (e.g. Analytics, Blog, Store)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new HMVC module with Layer 6 Encrypted Protection and AegisGuard BaseController';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $rawName = trim((string) $this->argument('name'));
        if (empty($rawName)) {
            $this->error("Nama modul tidak boleh kosong. Contoh: php artisan make:module Blog");
            return 1;
        }

        $name = Str::studly($rawName);
        $viewKey = strtolower($name);
        $modulePath = app_path("Modules/{$name}");
        $coreControllerPath = app_path('Http/Controllers/Core');

        if (File::isDirectory($modulePath)) {
            $this->error("Modul [{$name}] sudah ada di dalam direktori app/Modules/{$name}!");
            return 1;
        }

        $this->info("=======================================================");
        $this->info("       DAPCODE AEGISGUARD — CREATE NEW MODULE          ");
        $this->info("=======================================================");
        $this->line("Membuat struktur modul baru: [{$name}]...");

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
        return parent::moduleRender(\$view, \$data, \$return);
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
     * Accessible via: /{$viewKey}
     *
     * @param Request \$request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request \$request)
    {
        \$moduleKey = '{$viewKey}';
        return \$this->moduleRender('index', [
            'title' => "{$name} Module",
            'subtitle' => "DapCode AegisGuard Protected {$name} Module",
        ]);
    }
}
PHP;
        File::put("{$modulePath}/Controllers/{$name}.php", $controllerStub);

        // 5. Generate Main Model
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
            <p style="color: var(--text-muted); font-size: 13.5px;">Modul <strong>{$name}</strong> berhasil dibuat dan terlindungi oleh <strong>DapCode AegisGuard Layer 1–6</strong>.</p>
        </div>
    </div>

    <div class="stat-item">
        <div style="font-family: monospace; font-size: 13px; color: #38bdf8; word-break: break-all;">
            <i class="fa-solid fa-folder-tree"></i> Controller: app/Modules/{$name}/Controllers/{$name}.php<br>
            <i class="fa-solid fa-cube"></i> Base Core: app/Http/Controllers/Core/{$name}Controllers.php<br>
            <i class="fa-solid fa-shield-halved"></i> Security: Encrypted (.enc) Envelope Protection Active
        </div>
    </div>
</div>
BLADE;
        File::put("{$modulePath}/Views/index.blade.php", $viewStub);

        // 7. Automatically package & encrypt into AES-256-GCM envelope
        $instId = InstallationService::getInstallationId();
        $dummyLic = [
            'license_id'      => 'NEW-MOD-' . strtoupper(Str::random(6)),
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'modules'         => ['*'],
            'signature'       => 'init',
        ];

        $encRes = ModuleEncryptionService::encryptModule($name, $dummyLic);
        if ($encRes['success']) {
            ModuleEncryptionService::lockModule($name);
            $this->info("[SECURITY] Modul [{$name}] berhasil dipaketkan ke dalam format AES-256-GCM (.enc) & di-lock.");
        }

        // 8. Update Integrity Manifest
        IntegrityService::recordCoreFilesManifest();

        $this->info("=======================================================");
        $this->info("  [BERHASIL] Modul [{$name}] Selesai Dibuat!");
        $this->info("=======================================================");
        $this->line("<comment>Core Base:</comment>    <info>app/Http/Controllers/Core/{$name}Controllers.php</info>");
        $this->line("<comment>Encrypted (.enc):</comment> <info>app/Modules/{$name}/Encrypted/</info>");
        $this->line("<comment>View Template:</comment> <info>app/Modules/{$name}/Views/index.blade.php</info>");
        $this->line("<comment>Access URL:</comment>    <info>/{$viewKey}</info>");

        return 0;
    }
}
