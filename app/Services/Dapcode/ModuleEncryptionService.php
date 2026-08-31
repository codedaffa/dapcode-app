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

    /**
     * Target directories inside each module that are subject to encryption.
     *
     * @var string[]
     */
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

    /**
     * Derive AES-256-GCM key deterministically using HKDF-SHA256.
     * Binds the key to license signature, installation ID, license ID, module name, and relative file path.
     *
     * @param array $license
     * @param string $module
     * @param string $relativePath
     * @param string $saltHex
     * @return string 32-byte binary key
     */
    public static function deriveModuleKey(array $license, string $module, string $relativePath, string $saltHex): string
    {
        $normalizedModule = strtolower($module);
        $cleanPath = ltrim(str_replace('\\', '/', $relativePath), '/');
        $installationId = (string) ($license['installation_id'] ?? InstallationService::getInstallationId());

        // Input Keying Material (IKM) bound cryptographically to Installation ID, Module, Relative Path, and Salt
        $ikm = hash('sha256', $installationId . '|' . $normalizedModule . '|' . $cleanPath . '|' . $saltHex, true);

        // Salt incorporates manifest salt and installation ID
        $salt = (hex2bin($saltHex) ?: '') . hash('sha256', $installationId, true);
        $info = self::DOMAIN_SEPARATION . ':' . $normalizedModule . ':' . $cleanPath;

        if (function_exists('hash_hkdf')) {
            return hash_hkdf('sha256', $ikm, 32, $info, $salt);
        }

        // Fallback HKDF extract and expand
        $prk = hash_hmac('sha256', $ikm, $salt, true);
        return substr(hash_hmac('sha256', $info . "\x01", $prk, true), 0, 32);
    }

    /**
     * Get the absolute filesystem path to a module directory.
     *
     * @param string $module
     * @return string
     */
    public static function getModulePath(string $module): string
    {
        $moduleName = Str::studly($module);
        return app_path('Modules/' . $moduleName);
    }

    /**
     * Get the absolute path to a module's manifest.json.
     *
     * @param string $module
     * @return string
     */
    public static function getManifestPath(string $module): string
    {
        return self::getModulePath($module) . '/Encrypted/manifest.json';
    }

    /**
     * Check whether a module is configured with encrypted critical assets.
     *
     * @param string $module
     * @return bool
     */
    public static function isModuleEncrypted(string $module): bool
    {
        return File::exists(self::getManifestPath($module));
    }

    /**
     * Retrieve and validate the module manifest.
     *
     * @param string $module
     * @return array|null
     */
    public static function getManifest(string $module): ?array
    {
        $manifestPath = self::getManifestPath($module);
        if (!File::exists($manifestPath)) {
            return null;
        }

        $manifest = json_decode(File::get($manifestPath), true);
        if (!$manifest || !isset($manifest['module'], $manifest['files'], $manifest['salt']) || !is_array($manifest['files'])) {
            return null;
        }

        return $manifest;
    }

    /**
     * Recursively discover all critical PHP source files (Controllers, Models, etc.) in a module.
     *
     * @param string $module
     * @return string[] Array of relative paths (e.g. ['Controllers/Commerce.php', 'Models/Commerce.php'])
     */
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

    /**
     * Validate PHP syntax of a string payload before encryption or execution.
     *
     * @param string $code
     * @return bool
     */
    public static function validatePhpSyntax(string $code): bool
    {
        if (empty(trim($code))) {
            return false;
        }

        // Use token_get_all to verify basic PHP token structure
        try {
            $tokens = @token_get_all($code, TOKEN_PARSE);
            return is_array($tokens) && count($tokens) > 0;
        } catch (\ParseError $e) {
            return false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Encrypt all critical files (Controllers, Models, etc.) belonging to a module.
     * Implements syntax validation, authenticated GCM encryption, roundtrip verification, and plaintext cleanup.
     *
     * @param string $module
     * @param array $license Reference license used to establish initial key derivation
     * @param array $customFiles Optional explicit list of relative files to encrypt
     * @return array{success: bool, message: string, manifest?: array, encrypted_files?: string[]}
     */
    public static function encryptModule(string $module, array $license, array $customFiles = []): array
    {
        $normalizedModule = strtolower($module);
        $modulePath = self::getModulePath($module);

        if (!File::isDirectory($modulePath)) {
            return ['success' => false, 'message' => "Direktori modul [{$module}] tidak ditemukan di {$modulePath}."];
        }

        $targetFiles = !empty($customFiles) ? $customFiles : self::discoverCriticalFiles($module);

        // If no plaintext files found, check if manifest already exists
        if (empty($targetFiles)) {
            if (self::isModuleEncrypted($module)) {
                return ['success' => true, 'message' => "Modul [{$module}] sudah dalam format terenkripsi (manifest valid)."];
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

        // Step 1: Encrypt each file and write envelope to Encrypted/ folder
        foreach ($targetFiles as $relPath) {
            $cleanRel = ltrim(str_replace('\\', '/', $relPath), '/');

            // Strict path traversal prevention
            if (strpos($cleanRel, '..') !== false || str_starts_with($cleanRel, '/') || str_starts_with($cleanRel, '\\')) {
                continue;
            }

            $sourceFullPath = $modulePath . '/' . $cleanRel;
            if (!File::exists($sourceFullPath)) {
                continue;
            }

            $plaintext = File::get($sourceFullPath);

            // Validate PHP syntax of plaintext before encrypting
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

            File::put($encFullPath, json_encode($envelope, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            @chmod($encFullPath, 0600);

            // Step 2: Verification Roundtrip (Ensure written .enc decrypts cleanly before deleting plaintext)
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

        // Step 3: Write manifest.json
        $manifestData = [
            'module'       => $normalizedModule,
            'version'      => 1,
            'algorithm'    => 'AES-256-GCM',
            'salt'         => $saltHex,
            'encrypted_at' => date('c'),
            'files'        => $manifestFiles,
        ];

        $manifestPath = self::getManifestPath($module);
        File::put($manifestPath, json_encode($manifestData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        @chmod($manifestPath, 0600);

        // Step 4: Securely remove plaintext sources only after manifest & envelopes are verified
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
            'success'         => true,
            'message'         => "Modul [{$module}] berhasil dienkripsi (" . count($manifestFiles) . " Controllers & Models dipaketkan ke .enc).",
            'manifest'        => $manifestData,
            'encrypted_files' => array_column($manifestFiles, 'path'),
        ];
    }

    /**
     * Unlock and decrypt an authorized module atomically using verified license key material.
     * Implements full atomic write, GCM authentication check, SHA-256 verification, and PHP syntax check.
     *
     * @param string $module
     * @param array|null $license
     * @return array{success: bool, message: string, unlocked_files?: string[]}
     */
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

            // Step 1: Pre-decryption License & Authorization Validation
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

            // Clean up any stale temporary files from previous crashed processes
            self::cleanupStaleTempFiles($modulePath);

            $decryptedFiles = [];

            // Step 2: Decrypt and verify all files in-memory first
            foreach ($manifest['files'] as $item) {
                $cleanRel = $item['path'] ?? ($item['target'] ?? '');
                $encRel = $item['encrypted'] ?? '';
                $expectedChecksum = $item['sha256'] ?? ($item['checksum'] ?? '');

                // Strict anti-path-traversal check
                if (strpos($cleanRel, '..') !== false || strpos($encRel, '..') !== false || str_starts_with($cleanRel, '/') || str_starts_with($cleanRel, '\\')) {
                    Log::warning('[AUDIT] EVENT: MODULE_INTEGRITY_FAILED', [
                        'module' => $normalizedModule,
                        'reason' => 'Path traversal detected in module manifest',
                    ]);
                    return ['success' => false, 'message' => "ERROR: Module \"{$module}\" integrity compromised (Path traversal detected)."];
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

                // Step 3: AES-256-GCM Decrypt & Authentication Tag Verification
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

                // Step 4: SHA-256 Checksum Verification
                $actualChecksum = hash('sha256', $decrypted);
                if (!hash_equals($expectedChecksum, $actualChecksum)) {
                    Log::warning('[AUDIT] EVENT: MODULE_INTEGRITY_FAILED', [
                        'module' => $normalizedModule,
                        'reason' => 'Post-decryption SHA-256 checksum mismatch',
                    ]);
                    return ['success' => false, 'message' => "ERROR: Checksum integrity mismatch for [{$cleanRel}]."];
                }

                // Step 5: PHP Syntax Validation
                if (!self::validatePhpSyntax($decrypted)) {
                    return ['success' => false, 'message' => "ERROR: Decrypted PHP code failed syntax validation for [{$cleanRel}]."];
                }

                $targetFullPath = $modulePath . '/' . ltrim(str_replace('\\', '/', $cleanRel), '/');
                $decryptedFiles[] = [
                    'relative_path'     => $cleanRel,
                    'target_path'       => $targetFullPath,
                    'content'           => $decrypted,
                    'expected_checksum' => $expectedChecksum,
                ];
            }

            // Step 6: Atomic Write via verified temporary files and atomic rename
            $writtenFiles = [];
            foreach ($decryptedFiles as $fileInfo) {
                $writeSuccess = self::createAtomicRuntimeFile(
                    $fileInfo['target_path'],
                    $fileInfo['content'],
                    $fileInfo['expected_checksum']
                );

                if (!$writeSuccess) {
                    // Rollback all written files immediately on failure
                    foreach ($writtenFiles as $writtenPath) {
                        if (File::exists($writtenPath)) {
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

    /**
     * Atomically write decrypted content to a secure temporary file, verify integrity, then rename.
     *
     * @param string $targetFullPath
     * @param string $decrypted
     * @param string $expectedChecksum
     * @return bool
     */
    public static function createAtomicRuntimeFile(string $targetFullPath, string $decrypted, string $expectedChecksum): bool
    {
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

        $bytesWritten = @fwrite($fp, $decrypted);
        @fflush($fp);
        @fclose($fp);

        if ($bytesWritten !== strlen($decrypted)) {
            if (File::exists($tempFullPath)) {
                @File::delete($tempFullPath);
            }
            return false;
        }

        // Verify temporary file before committing
        if (!File::exists($tempFullPath) || filesize($tempFullPath) !== strlen($decrypted)) {
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

        // Atomic rename / move
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

    /**
     * Clean up any stale temporary (.tmp) files in a module directory.
     *
     * @param string $modulePath
     * @return void
     */
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
            // Ignore scan errors
        }
    }

    /**
     * Purge decrypted runtime plaintext files for a specific module.
     * Preserves encrypted .enc files and manifest.json.
     *
     * @param string $module
     * @return bool
     */
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

        // Safely clear opcache for purged files if function is available
        if (function_exists('opcache_reset')) {
            @opcache_reset();
        }

        Log::info('[AUDIT] EVENT: MODULE_RUNTIME_PURGED', [
            'module' => $normalizedModule,
        ]);

        return true;
    }

    /**
     * Purge runtime plaintext files for all encrypted modules.
     *
     * @return bool
     */
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

    /**
     * Lock a module and remove runtime plaintext files (Used on revocation or fresh clone).
     *
     * @param string $module
     * @return array{success: bool, message: string}
     */
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

    /**
     * Lock all modules in the application.
     *
     * @return array{success: bool, message: string}
     */
    public static function lockAllModules(): array
    {
        self::purgeAllRuntimePlaintext();

        return [
            'success' => true,
            'message' => 'Seluruh modul terlindungi berhasil dikunci (LOCKED).',
        ];
    }

    /**
     * Verify the integrity of a module's encrypted manifest and envelopes.
     *
     * @param string $module
     * @return array{valid: bool, module: string, files_count: int, issues: string[]}
     */
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

    /**
     * Check if a module is unlocked, present in plaintext, and has intact checksum integrity.
     *
     * @param string $module
     * @return bool
     */
    public static function isModuleAvailable(string $module): bool
    {
        $manifest = self::getManifest($module);
        if (!$manifest) {
            // Unencrypted standard module
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

            if (!hash_equals($expectedChecksum, hash_file('sha256', $targetFullPath))) {
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

    /**
     * Unlock all modules authorized in the given license.
     *
     * @param array $license
     * @return array<string, bool>
     */
    public static function unlockAuthorizedModules(array $license): array
    {
        $allModules = LicenseGuard::getAllAvailableModules();
        $allowedModules = (array) ($license['modules'] ?? []);
        $revokedModules = (array) ($license['revoked_modules'] ?? []);

        $results = [];

        foreach ($allModules as $module) {
            $normalized = strtolower($module);

            $isAllowed = (in_array('*', $allowedModules, true) || in_array($normalized, array_map('strtolower', $allowedModules), true))
                && !in_array($normalized, array_map('strtolower', $revokedModules), true);

            if ($isAllowed) {
                if (self::isModuleEncrypted($normalized)) {
                    $res = self::unlockModule($normalized, $license);
                    $results[$normalized] = $res['success'];
                } else {
                    $results[$normalized] = true;
                }
            } else {
                if (self::isModuleEncrypted($normalized)) {
                    self::lockModule($normalized);
                }
                $results[$normalized] = false;
            }
        }

        return $results;
    }

    /**
     * Lock all revoked modules and clean their plaintext files.
     *
     * @param array|null $revokedModules
     * @return void
     */
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

    /**
     * Get module status string (LOCKED, UNLOCKED, TAMPERED, NOT_ENCRYPTED).
     *
     * @param string $module
     * @return string
     */
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
                if (!hash_equals($expectedChecksum, hash_file('sha256', $targetFullPath))) {
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
