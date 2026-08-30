<?php

namespace App\Console\Commands;

use App\Services\Dapcode\InstallationService;
use App\Services\Dapcode\LicenseVerifier;
use Illuminate\Console\Command;

class SignDapcodeLicense extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dapcode:sign-license 
                            {installation_id? : Target Installation ID (default: current local installation)}
                            {--key= : Path to Authority Private Key PEM file}
                            {--passcode= : Secret Authority Passcode required for validation}
                            {--modules=* : Modules to allow (default: * for all modules)}
                            {--years=2 : License validity duration in years}
                            {--revoke : Generate a Signed Revocation Token instead of an activation license}
                            {--license_id= : Target License ID (required when generating revocation token)}
                            {--reason=Manual Revocation by Authority : Reason for revocation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DapCode License Authority CLI - Sign licenses and revocation tokens using isolated Owner Private Key and Secret Passcode';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("=======================================================");
        $this->info("   DAPCODE LICENSE AUTHORITY — CRYPTOGRAPHIC SIGNER    ");
        $this->info("=======================================================");

        // 1. Validate Secret Authority Passcode
        $passcode = $this->option('passcode');
        if (empty($passcode)) {
            $passcode = $this->secret('Masukkan Authority Secret Passcode:');
        }

        if (empty($passcode) || !LicenseVerifier::verifyPasscode($passcode)) {
            $this->newLine();
            $this->error("[SECURITY ERROR] Authority Secret Passcode salah! Akses generate payload ditolak.");
            $this->line("<comment>Kode rahasia tidak valid. Penandatanganan lisensi dibatalkan.</comment>");
            $this->newLine();
            return Command::FAILURE;
        }

        // 2. Resolve Authority Private Key path
        $keyPath = $this->option('key') 
            ?: env('DAPCODE_AUTHORITY_KEY_PATH') 
            ?: 'C:\Users\po\.gemini\antigravity-ide\brain\990a2152-70dc-4fd4-a1f5-79df37e16c3c\authority_private_key.pem';

        if (!file_exists($keyPath)) {
            $this->error("[SECURITY ERROR] Authority Private Key tidak ditemukan!");
            $this->line("<comment>File kunci privat tidak ditemukan di: {$keyPath}</comment>");
            $this->newLine();
            $this->line("Perintah ini membutuhkan <info>Private Signing Key</info> milik Pemilik (Owner).");
            $this->line("Gunakan opsi: <comment>--key=/path/to/authority_private_key.pem</comment>");
            $this->newLine();
            return Command::FAILURE;
        }

        $privateKeyContent = file_get_contents($keyPath);
        $res = @openssl_pkey_get_private($privateKeyContent);
        if ($res === false) {
            $this->error("[SECURITY ERROR] Format Private Key tidak valid atau rusak.");
            return Command::FAILURE;
        }

        $targetInstallationId = $this->argument('installation_id') ?: InstallationService::getInstallationId();

        // -------------------------------------------------------------
        // SCENARIO 1: Generate Signed Revocation Token
        // -------------------------------------------------------------
        if ($this->option('revoke')) {
            $licenseId = $this->option('license_id');
            if (empty($licenseId)) {
                $licFile = config('dapcode.files.license');
                if (file_exists($licFile)) {
                    $activeLic = json_decode(file_get_contents($licFile), true);
                    $licenseId = $activeLic['license_id'] ?? null;
                }
            }

            if (empty($licenseId)) {
                $this->error("Target --license_id diperlukan untuk membuat Signed Revocation Token.");
                return Command::FAILURE;
            }

            $reason = $this->option('reason') ?: 'Manual Revocation by Authority';
            $modulesOption = $this->option('modules');

            // Generate encrypted authority token for revocation
            $authToken = LicenseVerifier::generateAuthToken($licenseId, $targetInstallationId, 'REVOKE');

            $payload = [
                'action'          => 'REVOKE',
                'license_id'      => $licenseId,
                'installation_id' => $targetInstallationId,
                'revoked_at'      => date('c'),
                'reason'          => $reason,
                'auth_token'      => $authToken,
            ];

            if (!empty($modulesOption) && !in_array('*', $modulesOption, true)) {
                $payload['revoked_modules'] = $modulesOption;
            }

            $clean = $payload;
            ksort($clean);
            if (isset($clean['revoked_modules']) && is_array($clean['revoked_modules'])) {
                sort($clean['revoked_modules']);
            }
            $canonical = json_encode($clean, JSON_UNESCAPED_SLASHES);

            $binarySig = '';
            openssl_sign($canonical, $binarySig, $privateKeyContent, OPENSSL_ALGO_SHA256);
            $payload['signature'] = base64_encode($binarySig);

            $jsonOutput = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            $isGranular = !empty($payload['revoked_modules']);
            $this->warn($isGranular ? "TIPE: SIGNED GRANULAR MODULE REVOCATION TOKEN" : "TIPE: SIGNED FULL REVOCATION TOKEN");
            $this->line("Target Installation ID : <comment>{$targetInstallationId}</comment>");
            $this->line("Target License ID      : <comment>{$licenseId}</comment>");
            if ($isGranular) {
                $this->line("Modul yang Dicabut     : <comment>" . implode(', ', $payload['revoked_modules']) . "</comment>");
            }
            $this->line("Alasan Pencabutan      : <comment>{$reason}</comment>");
            $this->newLine();
            $this->info("Salin JSON berikut dan tempelkan ke form Pencabutan Lisensi di /dapcode/activate:");
            $this->newLine();
            $this->line($jsonOutput);
            $this->newLine();
            $this->info("=======================================================");
            return Command::SUCCESS;
        }

        // -------------------------------------------------------------
        // SCENARIO 2: Generate Signed Activation License Payload
        // -------------------------------------------------------------
        $modulesOption = $this->option('modules');
        $modules = !empty($modulesOption) ? $modulesOption : ['*'];
        $years = (int) $this->option('years') ?: 2;

        $licenseId = 'LIC-' . date('Y') . '-PRO-' . strtoupper(bin2hex(random_bytes(3)));
        $issuedAt = date('c');
        $expiresAt = date('c', strtotime("+{$years} years"));

        // Generate encrypted authority token
        $authToken = LicenseVerifier::generateAuthToken($licenseId, $targetInstallationId, 'ACTIVATE');

        $payload = [
            'license_id'      => $licenseId,
            'installation_id' => $targetInstallationId,
            'status'          => 'ACTIVE',
            'issued_at'       => $issuedAt,
            'expires_at'      => $expiresAt,
            'modules'         => $modules,
            'auth_token'      => $authToken,
        ];

        $canonical = LicenseVerifier::canonicalizePayload($payload);
        $binarySig = '';
        openssl_sign($canonical, $binarySig, $privateKeyContent, OPENSSL_ALGO_SHA256);
        $payload['signature'] = base64_encode($binarySig);

        $jsonOutput = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $this->info("TIPE: SIGNED ACTIVATION LICENSE (RSA-2048 + AUTH TOKEN)");
        $this->line("License ID             : <comment>{$licenseId}</comment>");
        $this->line("Target Installation ID : <comment>{$targetInstallationId}</comment>");
        $this->line("Modul Diizinkan        : <comment>" . implode(', ', $modules) . "</comment>");
        $this->line("Berlaku Sampai         : <comment>{$expiresAt}</comment>");
        $this->newLine();
        $this->info("Salin JSON payload berikut dan tempelkan ke form /dapcode/activate:");
        $this->newLine();
        $this->line($jsonOutput);
        $this->newLine();
        $this->info("=======================================================");

        return Command::SUCCESS;
    }
}
