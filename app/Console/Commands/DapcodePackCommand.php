<?php

namespace App\Console\Commands;

use App\Services\Dapcode\InstallationService;
use App\Services\Dapcode\IntegrityService;
use App\Services\Dapcode\LicenseGuard;
use App\Services\Dapcode\ModuleEncryptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DapcodePackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dapcode:pack {module=all : The module name to pack (e.g. Blog, Commerce, or "all")}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Package and re-encrypt latest module development code into AES-256-GCM (.php.enc) envelopes for Git/GitHub release';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $target = trim((string) $this->argument('module'));
        $instId = InstallationService::getInstallationId();
        $dummyLic = [
            'license_id'      => 'DEV-PACK-' . strtoupper(Str::random(6)),
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'modules'         => ['*'],
            'signature'       => 'dev_pack',
        ];

        $availableModules = LicenseGuard::getAllAvailableModules();

        if (empty($availableModules)) {
            $this->error("Tidak ada modul yang ditemukan di dalam direktori app/Modules/.");
            return 1;
        }

        $modulesToPack = [];
        if (strtolower($target) === 'all' || empty($target)) {
            $modulesToPack = $availableModules;
        } else {
            $found = null;
            foreach ($availableModules as $m) {
                if (strcasecmp($m, $target) === 0) {
                    $found = $m;
                    break;
                }
            }
            if (!$found) {
                $this->error("Modul [{$target}] tidak ditemukan di app/Modules/.");
                $this->line("Daftar modul yang tersedia: " . implode(', ', $availableModules));
                return 1;
            }
            $modulesToPack = [$found];
        }

        $this->info("=======================================================");
        $this->info("   DAPCODE AEGISGUARD — PACK & ENCRYPT LATEST CODE     ");
        $this->info("=======================================================");
        $this->line("Mengemas kode pengembangan terbaru ke dalam amplop enkripsi (.php.enc)...");
        $this->newLine();

        $successCount = 0;
        foreach ($modulesToPack as $mod) {
            $this->line("<comment>Packing modul:</comment> <info>{$mod}</info>...");
            
            // Check if plaintext files exist to pack
            $ctrlFile = app_path("Modules/{$mod}/Controllers/{$mod}.php");
            $hasPlaintext = file_exists($ctrlFile);

            if (!$hasPlaintext) {
                $this->line("  [INFO] File plaintext tidak ditemukan di disk. Menggunakan envelope .enc yang sudah ada.");
                continue;
            }

            $res = ModuleEncryptionService::encryptModule($mod, $dummyLic);
            if ($res['success']) {
                // Lock plaintext files for clean Git state
                ModuleEncryptionService::lockModule($mod);
                $this->info("  [OK] Berhasil mengemas {$res['encrypted_files_count']} file ke format .php.enc & di-lock.");
                $successCount++;
            } else {
                $this->error("  [FAIL] Gagal mengemas {$mod}: " . ($res['message'] ?? 'Error tidak diketahui'));
            }
        }

        // Update Layer 5 integrity manifest
        IntegrityService::recordCoreFilesManifest();

        $this->newLine();
        $this->info("=======================================================");
        $this->info("  [SELESAI] {$successCount} modul berhasil dipaketkan dengan kode terbaru!");
        $this->info("  Status: File .php.enc siap untuk di-commit & di-push ke GitHub.");
        $this->info("=======================================================");

        return 0;
    }
}
