<?php

namespace App\Services\Dapcode;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InstallationService
{
    /**
     * Get or create the unique, persistent Installation ID.
     *
     * @return string
     */
    public static function getInstallationId(): string
    {
        $filePath = config('dapcode.files.installation', storage_path('app/dapcode/.installation'));

        if (File::exists($filePath)) {
            $content = trim(File::get($filePath));
            if (!empty($content)) {
                return $content;
            }
        }

        // Generate persistent installation ID on first run
        $directory = dirname($filePath);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }

        $uniqueId = 'DAP-' . strtoupper(Str::random(6)) . '-' . Str::uuid()->toString();
        File::put($filePath, $uniqueId);

        // Secure file permissions
        @chmod($filePath, 0600);

        return $uniqueId;
    }

    /**
     * Get system environment fingerprint for integrity validation.
     *
     * @return array
     */
    public static function getSystemFingerprint(): array
    {
        return [
            'installation_id' => self::getInstallationId(),
            'app_name'        => config('app.name', 'DapCode'),
            'php_version'     => PHP_VERSION,
            'server_host'     => gethostname() ?: 'localhost',
            'created_at'      => date('Y-m-d H:i:s'),
        ];
    }
}
