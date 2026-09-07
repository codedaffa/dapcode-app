<?php

namespace App\Services\Dapcode;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ModuleEncryptionService
{
    public const CIPHER_ALGO = 'aes-256-gcm';
    public const GCM_TAG_LENGTH = 16;
    public const GCM_IV_LENGTH = 12;
    public const DOMAIN_SEPARATION = 'DAPCODE-AEGISGUARD-MODULE-V1';

    protected static $criticalDirectories = [
        'Controllers',
        'Models',
        'Services',
        'Repositories',
        'Actions',
        'Jobs',
        'Policies',
        'Helpers',
    ];

    public static function deriveModuleKey(array $license, string $module, string $relativePath, string $saltHex): string
    {
        $normalizedModule = strtolower($module);
        $cleanPath = ltrim(str_replace('\\', '/', $relativePath), '/');
        $installationId = (string) ($license['installation_id'] ?? InstallationService::getInstallationId());
        $ikm = hash('sha256', $installationId . '|' . $normalizedModule . '|' . $cleanPath . '|' . $saltHex, true);
        $salt = (hex2bin($saltHex) ?: '') . hash('sha256', $installationId, true);
        $info = self::DOMAIN_SEPARATION . ':' . $normalizedModule . ':' . $cleanPath;

        if (function_exists('hash_hkdf')) {
            return hash_hkdf('sha256', $ikm, 32, $info, $salt);
        }

        $prk = hash_hmac('sha256', $ikm, $salt, true);
        return substr(hash_hmac('sha256', $info . "\x01", $prk, true), 0, 32);
    }

    public static function getModulePath(string $module): string
    {
        $moduleName = Str::studly($module);
        return app_path('Modules/' . $moduleName);
    }

    public static function getMasterManifestPath(): string
    {
        return app_path('Services/Dapcode/modules-manifest.json');
    }

    public static function getMasterManifest(): ?array
    {
        $masterPath = self::getMasterManifestPath();
        if (!File::exists($masterPath)) {
            return null;
        }

        $decoded = json_decode(File::get($masterPath), true);
        return is_array($decoded) ? $decoded : null;
    }

    public static function saveMasterManifest(array $manifestData, bool $minify = true): bool
    {
        $masterPath = self::getMasterManifestPath();
        $dir = dirname($masterPath);
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }

        $manifestData['updated_at'] = date('c');
        $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        if (!$minify) {
            $flags |= JSON_PRETTY_PRINT;
        }
        $encoded = json_encode($manifestData, $flags);
        $result = File::put($masterPath, $encoded);
        @chmod($masterPath, 0644);

        return $result !== false;
    }

    public static function removeModuleFromMasterManifest(string $module): bool
    {
        $normalized = strtolower($module);
        $master = self::getMasterManifest();
        if ($master && isset($master['modules'][$normalized])) {
            unset($master['modules'][$normalized]);
            return self::saveMasterManifest($master);
        }
        return true;
    }

    public static function getManifestPath(?string $module = null): string
    {
        return self::getMasterManifestPath();
    }

    public static function isModuleEncrypted(string $module): bool
    {
        $normalized = strtolower($module);
        $master = self::getMasterManifest();
        if ($master && isset($master['modules'][$normalized]) && !empty($master['modules'][$normalized]['files'])) {
            return true;
        }

        $localManifest = self::getModulePath($module) . '/Encrypted/manifest.json';
        return File::exists($localManifest);
    }

    public static function getManifest(string $module): ?array
    {
        $normalized = strtolower($module);
        $master = self::getMasterManifest();
        if ($master && isset($master['modules'][$normalized])) {
            $modData = $master['modules'][$normalized];
            return [
                'module'       => $normalized,
                'version'      => $master['version'] ?? 1,
                'algorithm'    => $master['algorithm'] ?? self::CIPHER_ALGO,
                'salt'         => $modData['salt'] ?? '',
                'encrypted_at' => $modData['encrypted_at'] ?? null,
                'files'        => $modData['files'] ?? [],
            ];
        }

        $localManifest = self::getModulePath($module) . '/Encrypted/manifest.json';
        if (File::exists($localManifest)) {
            $manifest = json_decode(File::get($localManifest), true);
            if ($manifest && isset($manifest['module'], $manifest['files'], $manifest['salt']) && is_array($manifest['files'])) {
                return $manifest;
            }
        }

        return null;
    }

    public static function discoverCriticalFiles(string $module): array
    {
        $modulePath = str_replace('\\', '/', self::getModulePath($module));
        if (!File::isDirectory($modulePath)) {
            return [];
        }

        $discovered = [];
        foreach (self::$criticalDirectories as $dirName) {
            $dirPath = $modulePath . '/' . $dirName;
            if (File::isDirectory($dirPath)) {
                $files = File::allFiles($dirPath);
                foreach ($files as $file) {
                    $ext = strtolower($file->getExtension());
                    if ($ext === 'php') {
                        $normalizedFilePath = str_replace('\\', '/', $file->getPathname());
                        $rel = ltrim(str_replace($modulePath, '', $normalizedFilePath), '/');
                        $discovered[] = $rel;
                    }
                }
            }
        }

        return $discovered;
    }

    public static function validatePhpSyntax(string $code): bool
    {
        if (empty(trim($code))) {
            return false;
        }

        try {
            $tokens = @token_get_all($code, TOKEN_PARSE);
            return is_array($tokens) && count($tokens) > 0;
        } catch (\ParseError $e) {
            return false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function encryptModule(string $module, array $license, array $customFiles = []): array
    {
        $normalizedModule = strtolower($module);
        $modulePath = self::getModulePath($module);

        if (!File::isDirectory($modulePath)) {
            return ['success' => false, 'message' => "Direktori modul [{$module}] tidak ditemukan di {$modulePath}."];
        }

        $targetFiles = !empty($customFiles) ? $customFiles : self::discoverCriticalFiles($module);
        if (empty($targetFiles)) {
            if (self::isModuleEncrypted($module)) {
                return [
                    'success'               => true,
                    'message'               => "Modul [{$module}] sudah dalam format terenkripsi (manifest valid).",
                    'encrypted_files'       => [],
                    'encrypted_files_count' => 0,
                ];
            }
            return ['success' => false, 'message' => "Tidak ada file Controller/Model PHP ditemukan untuk dienkripsi pada modul [{$module}]."];
        }

        $saltHex = bin2hex(random_bytes(16));
        $encryptedDir = $modulePath . '/Encrypted';
        if (!File::isDirectory($encryptedDir)) {
            File::makeDirectory($encryptedDir, 0755, true, true);
        }

        $manifestFiles = [];
        $stagedEncryptedFiles = [];

        foreach ($targetFiles as $relPath) {
            $cleanRel = ltrim(str_replace('\\', '/', $relPath), '/');
            if (strpos($cleanRel, '..') !== false || str_starts_with($cleanRel, '/') || str_starts_with($cleanRel, '\\')) {
                continue;
            }

            $sourceFullPath = $modulePath . '/' . $cleanRel;
            if (!File::exists($sourceFullPath)) {
                continue;
            }

            $plaintext = File::get($sourceFullPath);
            // If the source file on disk is currently minified, prefer unminified canonical code from vault
            if (SourceMapVaultService::hasMap($sourceFullPath)) {
                $unminified = SourceMapVaultService::restoreOriginal($sourceFullPath);
                if ($unminified !== null && self::validatePhpSyntax($unminified)) {
                    $plaintext = $unminified;
                }
            }

            if (!self::validatePhpSyntax($plaintext)) {
                return ['success' => false, 'message' => "Syntax error terdeteksi pada source file: {$cleanRel}. Enkripsi dibatalkan."];
            }

            $checksum = hash('sha256', $plaintext);
            $fileSize = strlen($plaintext);
            $encryptionKey = self::deriveModuleKey($license, $normalizedModule, $cleanRel, $saltHex);
            $iv = random_bytes(self::GCM_IV_LENGTH);
            $tag = '';
            $aad = $normalizedModule . ':' . $cleanRel;

            $ciphertext = openssl_encrypt(
                $plaintext,
                self::CIPHER_ALGO,
                $encryptionKey,
                OPENSSL_RAW_DATA,
                $iv,
                $tag,
                $aad,
                self::GCM_TAG_LENGTH
            );

            if ($ciphertext === false || strlen($tag) !== self::GCM_TAG_LENGTH) {
                return ['success' => false, 'message' => "Gagal mengenkripsi file dengan AES-256-GCM: {$cleanRel}"];
            }

            $encRel = 'Encrypted/' . $cleanRel . '.enc';
            $encFullPath = $modulePath . '/' . $encRel;
            $encFileDir = dirname($encFullPath);
            if (!File::isDirectory($encFileDir)) {
                File::makeDirectory($encFileDir, 0755, true, true);
            }

            $envelope = [
                'version'       => 1,
                'algorithm'     => 'AES-256-GCM',
                'iv'            => base64_encode($iv),
                'tag'           => base64_encode($tag),
                'ciphertext'    => base64_encode($ciphertext),
                'sha256'        => $checksum,
                'size'          => $fileSize,
                'module'        => $normalizedModule,
                'relative_path' => $cleanRel,
            ];

            // Simpan amplop enkripsi (.enc) langsung dalam format MINIFIED (1 baris rapat)
            File::put($encFullPath, json_encode($envelope, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            @chmod($encFullPath, 0600);
            SourceMapVaultService::cleanMap($encFullPath);

            $roundtripDecrypted = openssl_decrypt(
                $ciphertext,
                self::CIPHER_ALGO,
                $encryptionKey,
                OPENSSL_RAW_DATA,
                $iv,
                $tag,
                $aad
            );

            if ($roundtripDecrypted === false || !hash_equals($checksum, hash('sha256', $roundtripDecrypted))) {
                @File::delete($encFullPath);
                return ['success' => false, 'message' => "Verifikasi roundtrip kriptografi gagal untuk file: {$cleanRel}. Plaintext dipertahankan."];
            }

            $manifestFiles[] = [
                'path'      => $cleanRel,
                'encrypted' => $encRel,
                'sha256'    => $checksum,
                'size'      => $fileSize,
            ];

            $stagedEncryptedFiles[] = [
                'source_path' => $sourceFullPath,
                'enc_path'    => $encFullPath,
            ];
        }

        if (empty($manifestFiles)) {
            return ['success' => false, 'message' => "Tidak ada file valid yang berhasil dienkripsi pada modul [{$module}]."];
        }

        $master = self::getMasterManifest() ?? [
            'version'   => 1,
            'algorithm' => 'AES-256-GCM',
            'modules'   => [],
        ];

        $master['modules'][$normalizedModule] = [
            'salt'         => $saltHex,
            'encrypted_at' => date('c'),
            'files'        => $manifestFiles,
        ];
        self::saveMasterManifest($master);
        SourceMapVaultService::cleanMap(self::getMasterManifestPath());

        $localManifest = self::getModulePath($module) . '/Encrypted/manifest.json';
        if (File::exists($localManifest)) {
            @File::delete($localManifest);
        }

        $manifestData = [
            'module'       => $normalizedModule,
            'version'      => 1,
            'algorithm'    => 'AES-256-GCM',
            'salt'         => $saltHex,
            'encrypted_at' => date('c'),
            'files'        => $manifestFiles,
        ];

        foreach ($stagedEncryptedFiles as $staged) {
            if (File::exists($staged['source_path'])) {
                File::delete($staged['source_path']);
            }
        }

        IntegrityService::recordCoreFilesManifest();
        LicenseGuard::clearCache();

        Log::info('[AUDIT] EVENT: MODULE_ENCRYPTED_PACKAGED', [
            'module' => $normalizedModule,
            'files'  => count($manifestFiles),
        ]);

        return [
            'success'               => true,
            'message'               => "Modul [{$module}] berhasil dienkripsi (" . count($manifestFiles) . " Controllers & Models dipaketkan ke .enc minified).",
            'manifest'              => $manifestData,
            'encrypted_files'       => array_column($manifestFiles, 'path'),
            'encrypted_files_count' => count($manifestFiles),
        ];
    }

    public static function verifyFileIntegrity(string $targetFullPath, string $expectedChecksum): bool
    {
        if (!File::exists($targetFullPath)) {
            return false;
        }

        $actualChecksum = hash_file('sha256', $targetFullPath);
        if (hash_equals($expectedChecksum, $actualChecksum)) {
            return true;
        }

        if (SourceMapVaultService::verifyMinifiedFile($targetFullPath, $expectedChecksum)) {
            return true;
        }

        $mapData = SourceMapVaultService::getVaultMapData($targetFullPath);
        if ($mapData) {
            $origHash = (string) ($mapData['orig_hash'] ?? '');
            $miniHash = (string) ($mapData['mini_hash'] ?? '');
            if ((hash_equals($origHash, $actualChecksum) && hash_equals($miniHash, $expectedChecksum)) ||
                (hash_equals($miniHash, $actualChecksum) && hash_equals($origHash, $expectedChecksum))) {
                return self::validatePhpSyntax(File::get($targetFullPath));
            }
        }

        if (self::validatePhpSyntax(File::get($targetFullPath))) {
            $minifiedOnDisk = @php_strip_whitespace($targetFullPath);
            if (!empty($minifiedOnDisk)) {
                if (hash_equals($expectedChecksum, hash('sha256', $minifiedOnDisk))) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function unlockModule(string $module, ?array $license = null): array
    {
        $normalizedModule = strtolower($module);
        Log::info('[AUDIT] EVENT: MODULE_UNLOCK_ATTEMPT', [
            'module' => $normalizedModule,
        ]);

        $lockDir = storage_path('app/dapcode');
        if (!File::isDirectory($lockDir)) {
            File::makeDirectory($lockDir, 0755, true, true);
        }

        $lockFilePath = $lockDir . '/.unlock.lock';
        $lockHandle = @fopen($lockFilePath, 'c+');
        if ($lockHandle) {
            @flock($lockHandle, LOCK_EX);
        }

        try {
            $licenseData = $license ?? LicenseGuard::getLicense();
            if (!$licenseData) {
                Log::warning('[AUDIT] EVENT: MODULE_UNLOCK_REJECTED', [
                    'module' => $normalizedModule,
                    'reason' => 'No active license found',
                ]);
                return ['success' => false, 'message' => "ERROR: Module \"{$module}\" cannot be unlocked.\nReason: Valid license authorization required."];
            }

            $verification = LicenseVerifier::verify($licenseData, $normalizedModule);
            if (!$verification['valid']) {
                Log::warning('[AUDIT] EVENT: MODULE_UNLOCK_REJECTED', [
                    'module' => $normalizedModule,
                    'reason' => $verification['reason'],
                ]);
                return ['success' => false, 'message' => "ERROR: Module \"{$module}\" cannot be unlocked.\nReason: " . $verification['reason']];
            }

            $manifest = self::getManifest($module);
            if (!$manifest) {
                return ['success' => true, 'message' => "Modul [{$module}] tidak memiliki file terenkripsi (Normal module)."];
            }

            $modulePath = self::getModulePath($module);
            $saltHex = (string) ($manifest['salt'] ?? '');
            self::cleanupStaleTempFiles($modulePath);

            $decryptedFiles = [];

            foreach ($manifest['files'] as $item) {
                $cleanRel = $item['path'] ?? ($item['target'] ?? '');
                $encRel = $item['encrypted'] ?? '';
                $expectedChecksum = $item['sha256'] ?? ($item['checksum'] ?? '');

                if (strpos($cleanRel, '..') !== false || strpos($encRel, '..') !== false || str_starts_with($cleanRel, '/') || str_starts_with($cleanRel, '\\')) {
                    Log::warning('[AUDIT] EVENT: MODULE_INTEGRITY_FAILED', [
                        'module' => $normalizedModule,
                        'reason' => 'Path traversal detected in module manifest',
                    ]);
                    return ['success' => false, 'message' => "ERROR: Module \"{$module}\" integrity compromised (Path traversal detected)."];
                }

                $targetFullPath = $modulePath . '/' . ltrim(str_replace('\\', '/', $cleanRel), '/');
                if (File::exists($targetFullPath)) {
                    $decryptedFiles[] = [
                        'relative_path'     => $cleanRel,
                        'target_path'       => $targetFullPath,
                        'content'           => null,
                        'expected_checksum' => $expectedChecksum,
                        'already_exists'    => true,
                    ];
                    continue;
                }

                $encFullPath = $modulePath . '/' . ltrim(str_replace('\\', '/', $encRel), '/');
                if (!File::exists($encFullPath)) {
                    Log::warning('[AUDIT] EVENT: MODULE_DECRYPT_FAILED', [
                        'module' => $normalizedModule,
                        'reason' => "Encrypted envelope missing: {$encRel}",
                    ]);
                    return ['success' => false, 'message' => "ERROR: Encrypted file [{$encRel}] not found."];
                }

                $envelope = json_decode(File::get($encFullPath), true);
                if (!$envelope || !isset($envelope['ciphertext'], $envelope['iv'], $envelope['tag'])) {
                    Log::warning('[AUDIT] EVENT: MODULE_DECRYPT_FAILED', [
                        'module' => $normalizedModule,
                        'reason' => 'Encrypted envelope format corrupted',
                    ]);
                    return ['success' => false, 'message' => "ERROR: Encrypted envelope format corrupted for [{$cleanRel}]."];
                }

                $iv = base64_decode($envelope['iv'], true);
                $tag = base64_decode($envelope['tag'], true);
                $ciphertext = base64_decode($envelope['ciphertext'], true);

                if ($iv === false || $tag === false || $ciphertext === false || strlen($iv) !== self::GCM_IV_LENGTH || strlen($tag) !== self::GCM_TAG_LENGTH) {
                    return ['success' => false, 'message' => "ERROR: Invalid cryptographic parameters in envelope [{$cleanRel}]."];
                }

                $decryptionKey = self::deriveModuleKey($licenseData, $normalizedModule, $cleanRel, $saltHex);
                $aad = $normalizedModule . ':' . $cleanRel;

                $decrypted = openssl_decrypt(
                    $ciphertext,
                    self::CIPHER_ALGO,
                    $decryptionKey,
                    OPENSSL_RAW_DATA,
                    $iv,
                    $tag,
                    $aad
                );

                if ($decrypted === false) {
                    Log::warning('[AUDIT] EVENT: MODULE_DECRYPT_FAILED', [
                        'module' => $normalizedModule,
                        'reason' => 'AES-GCM authentication tag verification failed',
                    ]);
                    return ['success' => false, 'message' => "ERROR: Cryptographic authentication failed for module [{$module}]. Decryption key invalid or ciphertext tampered."];
                }

                $actualChecksum = hash('sha256', $decrypted);
                if (!hash_equals($expectedChecksum, $actualChecksum)) {
                    Log::warning('[AUDIT] EVENT: MODULE_INTEGRITY_FAILED', [
                        'module' => $normalizedModule,
                        'reason' => 'Post-decryption SHA-256 checksum mismatch',
                    ]);
                    return ['success' => false, 'message' => "ERROR: Checksum integrity mismatch for [{$cleanRel}]."];
                }

                if (!self::validatePhpSyntax($decrypted)) {
                    return ['success' => false, 'message' => "ERROR: Decrypted PHP code failed syntax validation for [{$cleanRel}]."];
                }

                $decryptedFiles[] = [
                    'relative_path'     => $cleanRel,
                    'target_path'       => $targetFullPath,
                    'content'           => $decrypted,
                    'expected_checksum' => $expectedChecksum,
                    'already_exists'    => false,
                ];
            }

            $writtenFiles = [];
            foreach ($decryptedFiles as $fileInfo) {
                if (!empty($fileInfo['already_exists']) || File::exists($fileInfo['target_path'])) {
                    $writtenFiles[] = $fileInfo['target_path'];
                    continue;
                }

                $contentToWrite = $fileInfo['content'];
                $writeChecksum = $fileInfo['expected_checksum'];

                if (SourceMapVaultService::hasMap($fileInfo['target_path'])) {
                    $minifier = app(\App\Services\Dapcode\CodeMinifierService::class);
                    $contentToWrite = $minifier->minifyPhp($contentToWrite, '');
                    $writeChecksum = hash('sha256', $contentToWrite);
                    SourceMapVaultService::storeMap($fileInfo['target_path'], $fileInfo['content'], $contentToWrite);
                }

                $writeSuccess = self::createAtomicRuntimeFile(
                    $fileInfo['target_path'],
                    $contentToWrite,
                    $writeChecksum
                );

                if (!$writeSuccess) {
                    foreach ($writtenFiles as $writtenPath) {
                        if (File::exists($writtenPath) && !empty($fileInfo['content'])) {
                            @File::delete($writtenPath);
                        }
                    }
                    Log::warning('[AUDIT] EVENT: MODULE_DECRYPT_FAILED', [
                        'module' => $normalizedModule,
                        'reason' => 'Atomic write or temporary file verification failed',
                    ]);
                    return ['success' => false, 'message' => 'ERROR: Failed to write verified runtime file atomically.'];
                }

                $writtenFiles[] = $fileInfo['target_path'];
            }

            IntegrityService::recordCoreFilesManifest();
            LicenseGuard::clearCache();

            Log::info('[AUDIT] EVENT: MODULE_RUNTIME_CREATED', [
                'module' => $normalizedModule,
                'files'  => count($writtenFiles),
            ]);

            return [
                'success'        => true,
                'message'        => "Modul [{$module}] berhasil dibuka (UNLOCKED). " . count($writtenFiles) . " Controllers & Models dipulihkan.",
                'unlocked_files' => array_column($decryptedFiles, 'relative_path'),
            ];
        } finally {
            if ($lockHandle) {
                @flock($lockHandle, LOCK_UN);
                @fclose($lockHandle);
            }
        }
    }

    public static function createAtomicRuntimeFile(string $targetFullPath, string $content, string $expectedChecksum): bool
    {
        if (ActivationService::$isActivating && File::exists($targetFullPath)) {
            Log::warning('[SECURITY_GUARD] Blocked write operation to app/Modules during license activation', [
                'file' => $targetFullPath,
            ]);
            return false;
        }

        $targetDir = dirname($targetFullPath);
        if (!File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0755, true, true);
        }

        $tempFileName = '.' . basename($targetFullPath) . '.' . getmypid() . '.' . bin2hex(random_bytes(6)) . '.tmp';
        $tempFullPath = $targetDir . '/' . $tempFileName;

        $fp = @fopen($tempFullPath, 'wb');
        if (!$fp) {
            return false;
        }

        $bytesWritten = @fwrite($fp, $content);
        @fflush($fp);
        @fclose($fp);

        if ($bytesWritten !== strlen($content)) {
            if (File::exists($tempFullPath)) {
                @File::delete($tempFullPath);
            }
            return false;
        }

        if (!File::exists($tempFullPath) || filesize($tempFullPath) !== strlen($content)) {
            if (File::exists($tempFullPath)) {
                @File::delete($tempFullPath);
            }
            return false;
        }

        $tempChecksum = hash_file('sha256', $tempFullPath);
        if (!hash_equals($expectedChecksum, $tempChecksum)) {
            @File::delete($tempFullPath);
            return false;
        }

        $renamed = @rename($tempFullPath, $targetFullPath);
        if (!$renamed) {
            if (File::exists($targetFullPath)) {
                @File::delete($targetFullPath);
            }
            $renamed = @rename($tempFullPath, $targetFullPath);
        }

        if (!$renamed) {
            if (File::exists($tempFullPath)) {
                @File::delete($tempFullPath);
            }
            return false;
        }

        @chmod($targetFullPath, 0644);
        return true;
    }

    protected static function cleanupStaleTempFiles(string $modulePath): void
    {
        if (!File::isDirectory($modulePath)) {
            return;
        }

        try {
            $files = File::allFiles($modulePath);
            foreach ($files as $file) {
                if (str_ends_with($file->getFilename(), '.tmp')) {
                    @File::delete($file->getPathname());
                }
            }
        } catch (\Throwable $e) {
        }
    }

    public static function purgeRuntimePlaintext(string $module): bool
    {
        $normalizedModule = strtolower($module);
        $manifest = self::getManifest($module);
        if (!$manifest) {
            return true;
        }

        $modulePath = self::getModulePath($module);
        foreach ($manifest['files'] as $item) {
            $cleanRel = $item['path'] ?? ($item['target'] ?? '');
            if (!empty($cleanRel) && strpos($cleanRel, '..') === false) {
                $targetFullPath = $modulePath . '/' . ltrim(str_replace('\\', '/', $cleanRel), '/');
                if (File::exists($targetFullPath)) {
                    @File::delete($targetFullPath);
                }
            }
        }

        self::cleanupStaleTempFiles($modulePath);
        IntegrityService::recordCoreFilesManifest();
        LicenseGuard::clearCache();

        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }

        Log::info('[AUDIT] EVENT: MODULE_RUNTIME_PURGED', [
            'module' => $normalizedModule,
        ]);

        return true;
    }

    public static function purgeAllRuntimePlaintext(): bool
    {
        $allModules = LicenseGuard::getAllAvailableModules();
        foreach ($allModules as $mod) {
            if (self::isModuleEncrypted($mod)) {
                self::purgeRuntimePlaintext($mod);
            }
        }
        return true;
    }

    public static function lockModule(string $module): array
    {
        $normalizedModule = strtolower($module);
        $manifest = self::getManifest($module);
        if (!$manifest) {
            return ['success' => true, 'message' => "Modul [{$module}] bukan modul terenkripsi."];
        }

        self::purgeRuntimePlaintext($module);
        Log::info('[AUDIT] EVENT: MODULE_LOCKED', [
            'module' => $normalizedModule,
        ]);

        return [
            'success' => true,
            'message' => "Modul [{$module}] berhasil dikunci (LOCKED) dan file plaintext dibersihkan.",
        ];
    }

    public static function lockAllModules(): array
    {
        self::purgeAllRuntimePlaintext();
        return [
            'success' => true,
            'message' => 'Seluruh modul terlindungi berhasil dikunci (LOCKED).',
        ];
    }

    public static function verifyModule(string $module): array
    {
        $normalizedModule = strtolower($module);
        $manifest = self::getManifest($module);
        if (!$manifest) {
            return [
                'valid'       => true,
                'module'      => $normalizedModule,
                'status'      => 'NOT_ENCRYPTED',
                'files_count' => 0,
                'issues'      => [],
            ];
        }

        $modulePath = self::getModulePath($module);
        $issues = [];
        $filesCount = 0;

        foreach ($manifest['files'] as $item) {
            $filesCount++;
            $cleanRel = $item['path'] ?? ($item['target'] ?? '');
            $encRel = $item['encrypted'] ?? '';

            if (empty($cleanRel) || strpos($cleanRel, '..') !== false) {
                $issues[] = "Invalid path in manifest: {$cleanRel}";
                continue;
            }

            $encFullPath = $modulePath . '/' . ltrim(str_replace('\\', '/', $encRel), '/');
            if (!File::exists($encFullPath)) {
                $issues[] = "Missing encrypted envelope: {$encRel}";
                continue;
            }

            $envelope = json_decode(File::get($encFullPath), true);
            if (!$envelope || !isset($envelope['ciphertext'], $envelope['iv'], $envelope['tag'], $envelope['sha256'])) {
                $issues[] = "Corrupted encrypted envelope: {$encRel}";
            }
        }

        return [
            'valid'       => empty($issues),
            'module'      => $normalizedModule,
            'status'      => self::getModuleStatus($module),
            'files_count' => $filesCount,
            'issues'      => $issues,
        ];
    }

    public static function isModuleAvailable(string $module): bool
    {
        $manifest = self::getManifest($module);
        if (!$manifest) {
            return true;
        }

        $modulePath = self::getModulePath($module);
        foreach ($manifest['files'] as $item) {
            $cleanRel = $item['path'] ?? ($item['target'] ?? '');
            $expectedChecksum = $item['sha256'] ?? ($item['checksum'] ?? '');

            if (empty($cleanRel) || strpos($cleanRel, '..') !== false) {
                return false;
            }

            $targetFullPath = $modulePath . '/' . ltrim(str_replace('\\', '/', $cleanRel), '/');
            if (!File::exists($targetFullPath)) {
                return false;
            }

            if (!self::verifyFileIntegrity($targetFullPath, $expectedChecksum)) {
                Log::warning('[AUDIT] EVENT: MODULE_INTEGRITY_FAILED', [
                    'module' => strtolower($module),
                    'file'   => $cleanRel,
                    'reason' => 'Target file modified or corrupted',
                ]);
                return false;
            }
        }

        return true;
    }

    public static function unlockAuthorizedModules(array $license): array
    {
        $allModules = LicenseGuard::getAllAvailableModules();
        $allowedModules = (array) ($license['modules'] ?? []);
        $revokedModules = (array) ($license['revoked_modules'] ?? []);
        $results = [];

        foreach ($allModules as $module) {
            $normalized = strtolower($module);
            $isAllowed = (in_array('*', $allowedModules, true) || in_array($normalized, array_map('strtolower', $allowedModules), true)) && !in_array($normalized, array_map('strtolower', $revokedModules), true);

            if ($isAllowed) {
                if (self::isModuleEncrypted($normalized)) {
                    $res = self::unlockModule($normalized, $license);
                    $results[$normalized] = $res['success'];
                } else {
                    $results[$normalized] = true;
                }
            } else {
                $results[$normalized] = false;
            }
        }

        return $results;
    }

    public static function lockRevokedModules(?array $revokedModules = null): void
    {
        $allModules = LicenseGuard::getAllAvailableModules();
        $targets = $revokedModules !== null ? array_map('strtolower', $revokedModules) : $allModules;

        foreach ($targets as $module) {
            if (self::isModuleEncrypted($module)) {
                self::purgeRuntimePlaintext($module);
                Log::info('[AUDIT] EVENT: MODULE_REVOCATION_LOCK', [
                    'module' => $module,
                ]);
            }
        }
    }

    public static function getModuleStatus(string $module): string
    {
        if (!self::isModuleEncrypted($module)) {
            return 'NOT_ENCRYPTED';
        }

        $manifest = self::getManifest($module);
        $modulePath = self::getModulePath($module);
        $allPresent = true;
        $anyPresent = false;
        $integrityPass = true;

        foreach ($manifest['files'] as $item) {
            $cleanRel = $item['path'] ?? ($item['target'] ?? '');
            $expectedChecksum = $item['sha256'] ?? ($item['checksum'] ?? '');
            $targetFullPath = $modulePath . '/' . ltrim(str_replace('\\', '/', $cleanRel), '/');

            if (File::exists($targetFullPath)) {
                $anyPresent = true;
                if (!self::verifyFileIntegrity($targetFullPath, $expectedChecksum)) {
                    $integrityPass = false;
                }
            } else {
                $allPresent = false;
            }
        }

        if (!$integrityPass) {
            return 'TAMPERED';
        }

        if ($allPresent) {
            return 'UNLOCKED';
        }

        return 'LOCKED';
    }
}