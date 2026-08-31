<?php

namespace App\Console\Commands;

use App\Services\Dapcode\IntegrityService;
use App\Services\Dapcode\LicenseGuard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RemoveHMVCModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:module {name : The name of the module to delete (e.g. Blog, Store)} {--force : Force removal without interactive prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely delete an HMVC module and its Core BaseController, then update Layer 5 integrity manifest';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $rawName = trim((string) $this->argument('name'));
        if (empty($rawName)) {
            $this->error("Nama modul tidak boleh kosong. Contoh: php artisan remove:module Blog");
            return 1;
        }

        $name = Str::studly($rawName);
        $modulePath = app_path("Modules/{$name}");
        $coreBaseControllerFile = app_path("Http/Controllers/Core/{$name}Controllers.php");

        $moduleDirExists = File::isDirectory($modulePath);
        $coreControllerExists = File::exists($coreBaseControllerFile);

        if (!$moduleDirExists && !$coreControllerExists) {
            $this->error("Modul [{$name}] tidak ditemukan di dalam sistem!");
            return 1;
        }

        if (!$this->option('force') && $this->input->isInteractive()) {
            if (!$this->confirm("Apakah Anda yakin ingin MENGHAPUS modul [{$name}] beserta seluruh file dan Core Controller-nya?", false)) {
                $this->info("Operasi penghapusan modul dibatalkan.");
                return 0;
            }
        }

        $this->info("=======================================================");
        $this->info("       DAPCODE AEGISGUARD — REMOVE MODULE              ");
        $this->info("=======================================================");
        $this->line("Menghapus seluruh struktur modul [{$name}]...");

        $deletedItems = [];

        // 1. Delete app/Modules/{Name}
        if ($moduleDirExists) {
            File::deleteDirectory($modulePath);
            $deletedItems[] = "app/Modules/{$name}/ (Folder & Enkripsi .enc)";
        }

        // 2. Delete Core Base Controller if exists
        if ($coreControllerExists) {
            File::delete($coreBaseControllerFile);
            $deletedItems[] = "app/Http/Controllers/Core/{$name}Controllers.php";
        }

        // 3. Update Layer 5 integrity manifest
        IntegrityService::recordCoreFilesManifest();

        $this->newLine();
        foreach ($deletedItems as $item) {
            $this->line("<comment>Terhapus:</comment> <info>{$item}</info>");
        }

        $this->newLine();
        $this->info("=======================================================");
        $this->info("  [BERHASIL] Modul [{$name}] telah berhasil dihapus!");
        $this->info("  Integritas Layer 5 telah diperbarui secara otomatis.");
        $this->info("=======================================================");

        return 0;
    }
}
