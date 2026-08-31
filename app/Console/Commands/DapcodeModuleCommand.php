<?php

namespace App\Console\Commands;

use App\Services\Dapcode\LicenseGuard;
use App\Services\Dapcode\ModuleEncryptionService;
use Illuminate\Console\Command;

class DapcodeModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dapcode:module {action=status : Command action (status)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inspect module protection and license authorization status';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("=======================================================");
        $this->info("       DAPCODE AEGISGUARD — MODULE SECURITY STATUS     ");
        $this->info("=======================================================");

        $modules = LicenseGuard::getAllAvailableModules();
        $headers = ['Module', 'Encrypted', 'Status', 'Allowed In License'];
        $rows = [];

        $license = LicenseGuard::getLicense();
        $allowed = LicenseGuard::getAllowedModules();

        foreach ($modules as $mod) {
            $isEnc = ModuleEncryptionService::isModuleEncrypted($mod) ? 'YES' : 'NO';
            $status = ModuleEncryptionService::getModuleStatus($mod);
            $isAllowed = in_array(strtolower($mod), array_map('strtolower', $allowed), true) ? 'YES' : 'NO';

            $rows[] = [
                ucfirst($mod),
                $isEnc,
                $status,
                $isAllowed,
            ];
        }

        $this->table($headers, $rows);
        return Command::SUCCESS;
    }
}
