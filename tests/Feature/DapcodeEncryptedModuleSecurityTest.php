<?php

namespace Tests\Feature;

use App\Http\Controllers\Dapcode\LicenseController;
use App\Http\Middleware\DapcodeLicenseMiddleware;
use App\Services\Dapcode\ActivationService;
use App\Services\Dapcode\InstallationService;
use App\Services\Dapcode\IntegrityService;
use App\Services\Dapcode\LicenseGuard;
use App\Services\Dapcode\LicenseVerifier;
use App\Services\Dapcode\ModuleEncryptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

class DapcodeEncryptedModuleSecurityTest extends TestCase
{
    /** @var string */
    protected $privKeyPath = 'C:\Users\po\.gemini\antigravity-ide\brain\990a2152-70dc-4fd4-a1f5-79df37e16c3c\authority_private_key.pem';

    /** @var string */
    protected $authorityPrivateKey = '';

    /** @var array<string, string> Backup of original controller contents */
    protected static $originalControllers = [];

    /** @var array<string, string> Backup of original model contents */
    protected static $originalModels = [];

    /** @var array<string, array<string, string>> Backup of original encrypted files per module */
    protected static $originalEncryptedFiles = [];

    /** @var string|null Backup of original master manifest */
    protected static $originalMasterManifest = null;

    protected function setUp(): void
    {
        parent::setUp();
        if (file_exists($this->privKeyPath)) {
            $this->authorityPrivateKey = file_get_contents($this->privKeyPath);
        }

        if (self::$originalMasterManifest === null && File::exists(ModuleEncryptionService::getMasterManifestPath())) {
            self::$originalMasterManifest = File::get(ModuleEncryptionService::getMasterManifestPath());
        }

        // Backup all controllers, models, and original encrypted payloads
        $allMods = LicenseGuard::getAllAvailableModules();
        foreach ($allMods as $rawMod) {
            $mod = Str::studly($rawMod);
            $ctrl = app_path("Modules/{$mod}/Controllers/{$mod}.php");
            if (!isset(self::$originalControllers[$mod]) && File::exists($ctrl)) {
                self::$originalControllers[$mod] = File::get($ctrl);
            }
            $model = app_path("Modules/{$mod}/Models/{$mod}.php");
            if (!isset(self::$originalModels[$mod]) && File::exists($model)) {
                self::$originalModels[$mod] = File::get($model);
            }
            $encDir = app_path("Modules/{$mod}/Encrypted");
            if (!isset(self::$originalEncryptedFiles[$mod]) && File::isDirectory($encDir)) {
                self::$originalEncryptedFiles[$mod] = [];
                foreach (File::allFiles($encDir) as $f) {
                    $rel = ltrim(str_replace(str_replace('\\', '/', $encDir), '', str_replace('\\', '/', $f->getPathname())), '/');
                    self::$originalEncryptedFiles[$mod][$rel] = File::get($f->getPathname());
                }
            }
        }

        $this->restorePlaintextFiles();
        $this->resetLicenseFiles();
        IntegrityService::recordCoreFilesManifest();
        LicenseGuard::clearCache();
    }

    protected function tearDown(): void
    {
        $this->resetLicenseFiles();
        $this->restorePlaintextFiles();
        if (self::$originalMasterManifest !== null) {
            File::put(ModuleEncryptionService::getMasterManifestPath(), self::$originalMasterManifest);
        }
        IntegrityService::recordCoreFilesManifest();
        LicenseGuard::clearCache();
        parent::tearDown();
    }

    protected function restorePlaintextFiles(): void
    {
        $allMods = LicenseGuard::getAllAvailableModules();
        foreach ($allMods as $rawMod) {
            $mod = Str::studly($rawMod);
            $ctrlPath = app_path("Modules/{$mod}/Controllers/{$mod}.php");
            if (isset(self::$originalControllers[$mod])) {
                File::put($ctrlPath, self::$originalControllers[$mod]);
            }

            $modelPath = app_path("Modules/{$mod}/Models/{$mod}.php");
            if (isset(self::$originalModels[$mod])) {
                File::put($modelPath, self::$originalModels[$mod]);
            }

            $encDir = app_path("Modules/{$mod}/Encrypted");
            if (isset(self::$originalEncryptedFiles[$mod])) {
                if (File::isDirectory($encDir)) {
                    File::deleteDirectory($encDir);
                }
                File::makeDirectory($encDir, 0755, true, true);
                foreach (self::$originalEncryptedFiles[$mod] as $rel => $encContent) {
                    $fullPath = $encDir . '/' . $rel;
                    if (!File::isDirectory(dirname($fullPath))) {
                        File::makeDirectory(dirname($fullPath), 0755, true, true);
                    }
                    File::put($fullPath, $encContent);
                }
            }
        }

        if (self::$originalMasterManifest !== null) {
            File::put(ModuleEncryptionService::getMasterManifestPath(), self::$originalMasterManifest);
        }
    }

    protected function resetLicenseFiles(): void
    {
        LicenseGuard::clearCache();
        $licenseFile = config('dapcode.files.license');
        $stateFile = config('dapcode.files.license_state');
        if (File::exists($licenseFile)) File::delete($licenseFile);
        if (File::exists($stateFile)) File::delete($stateFile);
    }

    protected function authoritySign(array $payload): array
    {
        $action = strtoupper($payload['action'] ?? 'ACTIVATE');
        if (empty($payload['auth_token']) && isset($payload['license_id'], $payload['installation_id'])) {
            $payload['auth_token'] = LicenseVerifier::generateAuthToken((string) $payload['license_id'], (string) $payload['installation_id'], $action);
        }
        $clean = $payload;
        unset($clean['signature'], $clean['activated_at'], $clean['revoked_at'], $clean['revocation_reason'], $clean['revoked_modules']);
        ksort($clean);
        if (isset($clean['modules']) && is_array($clean['modules'])) {
            sort($clean['modules']);
        }
        $canonical = json_encode($clean, JSON_UNESCAPED_SLASHES);
        
        $binarySig = '';
        openssl_sign($canonical, $binarySig, $this->authorityPrivateKey, OPENSSL_ALGO_SHA256);
        $payload['signature'] = base64_encode($binarySig);
        return $payload;
    }

    protected function authorityRevoke(string $licenseId, string $installationId, string $reason = 'Manual Revocation', ?array $revokedModules = null): array
    {
        $payload = [
            'action'          => empty($revokedModules) ? 'REVOKE' : 'REVOKE_MODULES',
            'license_id'      => $licenseId,
            'installation_id' => $installationId,
            'revoked_at'      => date('c'),
            'reason'          => $reason,
            'auth_token'      => LicenseVerifier::generateAuthToken($licenseId, $installationId, 'REVOKE'),
        ];
        if (!empty($revokedModules)) {
            $payload['revoked_modules'] = $revokedModules;
        }
        $clean = $payload;
        ksort($clean);
        if (isset($clean['revoked_modules']) && is_array($clean['revoked_modules'])) {
            sort($clean['revoked_modules']);
        }
        $canonical = json_encode($clean, JSON_UNESCAPED_SLASHES);
        $binarySig = '';
        openssl_sign($canonical, $binarySig, $this->authorityPrivateKey, OPENSSL_ALGO_SHA256);
        $payload['signature'] = base64_encode($binarySig);
        return $payload;
    }

    protected function createEncryptedModuleState(string $module = 'Commerce'): array
    {
        $modStudly = \Illuminate\Support\Str::studly($module);
        $ctrlPath = app_path("Modules/{$modStudly}/Controllers/{$modStudly}.php");
        if (!File::exists($ctrlPath)) {
            $ctrlContent = self::$originalControllers[$modStudly] ?? "<?php\nnamespace App\Modules\\{$modStudly}\\Controllers;\nuse App\Http\Controllers\Controller;\nclass {$modStudly} extends Controller {\n    public function index() { return response()->json(['module' => '{$modStudly}']); }\n}\n";
            File::put($ctrlPath, $ctrlContent);
        }
        $modelPath = app_path("Modules/{$modStudly}/Models/{$modStudly}.php");
        if (!File::exists($modelPath)) {
            $modelContent = self::$originalModels[$modStudly] ?? "<?php\nnamespace App\Modules\\{$modStudly}\\Models;\nuse Illuminate\Database\Eloquent\Model;\nclass {$modStudly} extends Model {\n    protected \$table = '" . strtolower($modStudly) . "';\n}\n";
            File::put($modelPath, $modelContent);
        }

        $instId = InstallationService::getInstallationId();
        $licensePayload = $this->authoritySign([
            'license_id'      => 'LIC-2026-TEST-' . strtoupper(bin2hex(random_bytes(3))),
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => [strtolower($module), 'career'],
        ]);

        $res = ModuleEncryptionService::encryptModule($module, $licensePayload);
        $this->assertTrue($res['success'], "Module encryption failed: " . ($res['message'] ?? ''));

        // Lock to fresh clone state (remove plaintext)
        ModuleEncryptionService::lockModule($module);
        $this->resetLicenseFiles();

        return $licensePayload;
    }

    // 1. Fresh clone has no plaintext protected Controllers
    public function test_01_fresh_clone_has_no_plaintext_protected_controllers()
    {
        $this->createEncryptedModuleState('Commerce');
        $ctrlPath = app_path('Modules/Commerce/Controllers/Commerce.php');
        $this->assertFileDoesNotExist($ctrlPath);
        $this->assertTrue(ModuleEncryptionService::isModuleEncrypted('Commerce'));
        $this->assertFileExists(ModuleEncryptionService::getMasterManifestPath());
    }

    // 2. Fresh clone has no plaintext protected Models
    public function test_02_fresh_clone_has_no_plaintext_protected_models()
    {
        $this->createEncryptedModuleState('Commerce');
        $modelPath = app_path('Modules/Commerce/Models/Commerce.php');
        $this->assertFileDoesNotExist($modelPath);
    }

    // 3. Fresh clone denies protected modules
    public function test_03_fresh_clone_denies_protected_modules()
    {
        $this->createEncryptedModuleState('Commerce');
        $response = $this->get('/commerce');
        $response->assertStatus(403);
    }

    // 4. Activation unlocks authorized module
    public function test_04_activation_unlocks_authorized_module()
    {
        $license = $this->createEncryptedModuleState('Commerce');
        $actResult = ActivationService::activate($license);
        $this->assertTrue($actResult['success']);

        $ctrlPath = app_path('Modules/Commerce/Controllers/Commerce.php');
        $this->assertFileExists($ctrlPath);
        $this->assertEquals('UNLOCKED', ModuleEncryptionService::getModuleStatus('Commerce'));

        $response = $this->get('/commerce');
        $response->assertStatus(200);
    }

    // 5. Activation does not unlock unauthorized module
    public function test_05_activation_does_not_unlock_unauthorized_module()
    {
        $instId = InstallationService::getInstallationId();
        $this->createEncryptedModuleState('Commerce');
        $this->createEncryptedModuleState('Career');

        $singleModLicense = $this->authoritySign([
            'license_id'      => 'LIC-2026-COMMERCE-ONLY',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['commerce'], // Career excluded
        ]);

        ActivationService::activate($singleModLicense);

        $this->assertEquals('UNLOCKED', ModuleEncryptionService::getModuleStatus('Commerce'));
        $this->assertEquals('LOCKED', ModuleEncryptionService::getModuleStatus('Career'));
        $this->assertFileDoesNotExist(app_path('Modules/Career/Controllers/Career.php'));

        $this->get('/career')->assertStatus(403);
    }

    // 6. Activate-all unlocks all modules
    public function test_06_activate_all_unlocks_all_modules()
    {
        $instId = InstallationService::getInstallationId();
        $this->createEncryptedModuleState('Commerce');
        $this->createEncryptedModuleState('Career');

        $wildcardLicense = $this->authoritySign([
            'license_id'      => 'LIC-2026-WILDCARD',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['*'],
        ]);

        ActivationService::activate($wildcardLicense);

        $this->assertEquals('UNLOCKED', ModuleEncryptionService::getModuleStatus('Commerce'));
        $this->assertEquals('UNLOCKED', ModuleEncryptionService::getModuleStatus('Career'));
        $this->get('/commerce')->assertStatus(200);
        $this->get('/career')->assertStatus(200);
    }

    // 7. Revoke-all locks all modules
    public function test_07_revoke_all_locks_all_modules()
    {
        $instId = InstallationService::getInstallationId();
        $license = $this->createEncryptedModuleState('Commerce');
        $this->createEncryptedModuleState('Career');

        $activeLicense = $this->authoritySign([
            'license_id'      => 'LIC-2026-REVOKE-TEST',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['*'],
        ]);

        ActivationService::activate($activeLicense);
        $this->assertEquals('UNLOCKED', ModuleEncryptionService::getModuleStatus('Commerce'));

        // Sign revocation token
        $revokeToken = $this->authorityRevoke('LIC-2026-REVOKE-TEST', $instId, 'Security test full revoke');

        $deactRes = ActivationService::deactivate($revokeToken);
        $this->assertTrue($deactRes['success']);

        $this->assertEquals('LOCKED', ModuleEncryptionService::getModuleStatus('Commerce'));
        $this->assertEquals('LOCKED', ModuleEncryptionService::getModuleStatus('Career'));
        $this->assertFileDoesNotExist(app_path('Modules/Commerce/Controllers/Commerce.php'));
        $this->assertFileDoesNotExist(app_path('Modules/Career/Controllers/Career.php'));

        $this->get('/commerce')->assertStatus(403);
    }

    // 8. Revoking specific module removes plaintext
    public function test_08_revoking_specific_module_removes_plaintext()
    {
        $instId = InstallationService::getInstallationId();
        $this->createEncryptedModuleState('Commerce');
        $this->createEncryptedModuleState('Career');

        $activeLicense = $this->authoritySign([
            'license_id'      => 'LIC-2026-GRANULAR-TEST',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['commerce', 'career'],
        ]);

        ActivationService::activate($activeLicense);

        $granularToken = $this->authorityRevoke('LIC-2026-GRANULAR-TEST', $instId, 'Granular revocation of Commerce', ['commerce']);

        $res = ActivationService::deactivate($granularToken);
        $this->assertTrue($res['success']);

        $this->assertEquals('LOCKED', ModuleEncryptionService::getModuleStatus('Commerce'));
        $this->assertEquals('UNLOCKED', ModuleEncryptionService::getModuleStatus('Career'));
        $this->assertFileDoesNotExist(app_path('Modules/Commerce/Controllers/Commerce.php'));
        $this->assertFileExists(app_path('Modules/Career/Controllers/Career.php'));
    }

    // 9. Invalid signature cannot unlock
    public function test_09_invalid_signature_cannot_unlock()
    {
        $license = $this->createEncryptedModuleState('Commerce');
        $license['signature'] = base64_encode('fake-invalid-signature-' . random_bytes(64));

        $res = ModuleEncryptionService::unlockModule('Commerce', $license);
        $this->assertFalse($res['success']);
        $this->assertEquals('LOCKED', ModuleEncryptionService::getModuleStatus('Commerce'));
    }

    // 10. Expired license cannot unlock
    public function test_10_expired_license_cannot_unlock()
    {
        $instId = InstallationService::getInstallationId();
        $this->createEncryptedModuleState('Commerce');

        $expiredLicense = $this->authoritySign([
            'license_id'      => 'LIC-2026-EXPIRED',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c', strtotime('-3 years')),
            'expires_at'      => date('c', strtotime('-1 year')),
            'modules'         => ['*'],
        ]);

        $res = ModuleEncryptionService::unlockModule('Commerce', $expiredLicense);
        $this->assertFalse($res['success']);
        $this->assertEquals('LOCKED', ModuleEncryptionService::getModuleStatus('Commerce'));
    }

    // 11. Wrong installation cannot unlock
    public function test_11_wrong_installation_cannot_unlock()
    {
        $this->createEncryptedModuleState('Commerce');

        $mismatchedLicense = $this->authoritySign([
            'license_id'      => 'LIC-2026-OTHER-MACHINE',
            'installation_id' => 'DAP-OTHER-MACHINE-XYZ',
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['*'],
        ]);

        $res = ModuleEncryptionService::unlockModule('Commerce', $mismatchedLicense);
        $this->assertFalse($res['success']);
        $this->assertEquals('LOCKED', ModuleEncryptionService::getModuleStatus('Commerce'));
    }

    // 12. Forged license cannot unlock
    public function test_12_forged_license_cannot_unlock()
    {
        $license = $this->createEncryptedModuleState('Commerce');
        $license['license_id'] = 'LIC-FORGED-ID-9999';

        $res = ModuleEncryptionService::unlockModule('Commerce', $license);
        $this->assertFalse($res['success']);
    }

    // 13. Forged module authorization cannot unlock
    public function test_13_forged_module_authorization_cannot_unlock()
    {
        $instId = InstallationService::getInstallationId();
        $this->createEncryptedModuleState('Commerce');

        $license = $this->authoritySign([
            'license_id'      => 'LIC-2026-PROFILE-ONLY',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['profile'],
        ]);

        // Attacker modifies modules array without new RSA signature
        $license['modules'][] = 'commerce';

        $res = ModuleEncryptionService::unlockModule('Commerce', $license);
        $this->assertFalse($res['success']);
    }

    // 14. Modified ciphertext cannot decrypt
    public function test_14_modified_ciphertext_cannot_decrypt()
    {
        $license = $this->createEncryptedModuleState('Commerce');
        $encFile = app_path('Modules/Commerce/Encrypted/Controllers/Commerce.php.enc');
        $this->assertFileExists($encFile);

        $envelope = json_decode(File::get($encFile), true);
        $cipherBytes = base64_decode($envelope['ciphertext']);
        $cipherBytes[0] = chr(ord($cipherBytes[0]) ^ 0xFF);
        $envelope['ciphertext'] = base64_encode($cipherBytes);
        File::put($encFile, json_encode($envelope));

        $res = ModuleEncryptionService::unlockModule('Commerce', $license);
        $this->assertFalse($res['success']);
        $this->assertEquals('LOCKED', ModuleEncryptionService::getModuleStatus('Commerce'));
    }

    // 15. Modified authentication tag cannot decrypt
    public function test_15_modified_authentication_tag_cannot_decrypt()
    {
        $license = $this->createEncryptedModuleState('Commerce');
        $encFile = app_path('Modules/Commerce/Encrypted/Controllers/Commerce.php.enc');

        $envelope = json_decode(File::get($encFile), true);
        $tagBytes = base64_decode($envelope['tag']);
        $tagBytes[0] = chr(ord($tagBytes[0]) ^ 0xFF);
        $envelope['tag'] = base64_encode($tagBytes);
        File::put($encFile, json_encode($envelope));

        $res = ModuleEncryptionService::unlockModule('Commerce', $license);
        $this->assertFalse($res['success']);
    }

    // 16. Modified IV cannot decrypt
    public function test_16_modified_iv_cannot_decrypt()
    {
        $license = $this->createEncryptedModuleState('Commerce');
        $encFile = app_path('Modules/Commerce/Encrypted/Controllers/Commerce.php.enc');

        $envelope = json_decode(File::get($encFile), true);
        $ivBytes = base64_decode($envelope['iv']);
        $ivBytes[0] = chr(ord($ivBytes[0]) ^ 0xFF);
        $envelope['iv'] = base64_encode($ivBytes);
        File::put($encFile, json_encode($envelope));

        $res = ModuleEncryptionService::unlockModule('Commerce', $license);
        $this->assertFalse($res['success']);
    }

    // 17. Modified manifest fails
    public function test_17_modified_manifest_fails()
    {
        $license = $this->createEncryptedModuleState('Commerce');
        $master = ModuleEncryptionService::getMasterManifest();
        $master['modules']['commerce']['salt'] = bin2hex(random_bytes(16)); // Tamper salt
        ModuleEncryptionService::saveMasterManifest($master);

        $res = ModuleEncryptionService::unlockModule('Commerce', $license);
        $this->assertFalse($res['success']);
    }

    // 18. Checksum mismatch fails
    public function test_18_checksum_mismatch_fails()
    {
        $license = $this->createEncryptedModuleState('Commerce');
        $master = ModuleEncryptionService::getMasterManifest();
        $master['modules']['commerce']['files'][0]['sha256'] = hash('sha256', 'tampered-payload-expectation');
        ModuleEncryptionService::saveMasterManifest($master);

        $res = ModuleEncryptionService::unlockModule('Commerce', $license);
        $this->assertFalse($res['success']);
    }

    // 19. Path traversal is rejected
    public function test_19_path_traversal_is_rejected()
    {
        $license = $this->createEncryptedModuleState('Commerce');
        $master = ModuleEncryptionService::getMasterManifest();
        $master['modules']['commerce']['files'][0]['path'] = '../../../../public/hacked.php';
        ModuleEncryptionService::saveMasterManifest($master);

        $res = ModuleEncryptionService::unlockModule('Commerce', $license);
        $this->assertFalse($res['success']);
        $this->assertFileDoesNotExist(public_path('hacked.php'));
    }

    // 20. Direct Module unlock without valid license fails
    public function test_20_command_unlock_without_license_fails()
    {
        $this->createEncryptedModuleState('Commerce');
        $res = ModuleEncryptionService::unlockModule('Commerce', null);
        $this->assertFalse($res['success']);
        $this->assertEquals('LOCKED', ModuleEncryptionService::getModuleStatus('Commerce'));
    }

    // 21. Middleware bypass does not restore encrypted source
    public function test_21_middleware_bypass_does_not_restore_encrypted_source()
    {
        $this->createEncryptedModuleState('Commerce');
        $this->assertFileDoesNotExist(app_path('Modules/Commerce/Controllers/Commerce.php'));

        $middleware = new DapcodeLicenseMiddleware();
        $request = Request::create('/commerce', 'GET');

        // Attacker simulates disabled middleware return $next($request)
        $simulatedPass = false;
        try {
            $middleware->handle($request, function ($req) use (&$simulatedPass) {
                $simulatedPass = true;
                return response('Bypassed Middleware', 200);
            }, 'commerce');
        } catch (\Throwable $e) {
            // Layer 2/3/6 blocks execution
        }

        // Encrypted source is STILL absent on disk
        $this->assertFileDoesNotExist(app_path('Modules/Commerce/Controllers/Commerce.php'));
    }

    // 22. Public activation page remains accessible
    public function test_22_public_activation_page_remains_accessible()
    {
        $this->createEncryptedModuleState('Commerce');
        $response = $this->get('/dapcode/activate');
        $response->assertStatus(200);
    }

    // 23. Protected controller is unavailable when encrypted
    public function test_23_protected_controller_is_unavailable_when_encrypted()
    {
        $this->createEncryptedModuleState('Commerce');
        $ctrlPath = app_path('Modules/Commerce/Controllers/Commerce.php');
        $this->assertFileDoesNotExist($ctrlPath);
    }

    // 24. Protected model is unavailable when encrypted
    public function test_24_protected_model_is_unavailable_when_encrypted()
    {
        $this->createEncryptedModuleState('Commerce');
        $modelPath = app_path('Modules/Commerce/Models/Commerce.php');
        $this->assertFileDoesNotExist($modelPath);
    }

    // 25. Activate -> execute -> revoke -> execute fails
    public function test_25_activate_execute_revoke_execute_fails()
    {
        $instId = InstallationService::getInstallationId();
        $license = $this->createEncryptedModuleState('Commerce');

        $activeLicense = $this->authoritySign([
            'license_id'      => 'LIC-2026-LIFECYCLE-E2E',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['commerce'],
        ]);

        // Phase 1: Activate
        $actRes = ActivationService::activate($activeLicense);
        $this->assertTrue($actRes['success']);

        // Phase 2: Execute (Success)
        $this->get('/commerce')->assertStatus(200);

        // Phase 3: Revoke
        $revokeToken = $this->authorityRevoke('LIC-2026-LIFECYCLE-E2E', $instId, 'End of lifecycle test');
        $deactRes = ActivationService::deactivate($revokeToken);
        $this->assertTrue($deactRes['success']);

        // Phase 4: Execute after revoke (Must Fail 403)
        $this->get('/commerce')->assertStatus(403);
        $this->assertFileDoesNotExist(app_path('Modules/Commerce/Controllers/Commerce.php'));
    }

    // 26. Minified module remains available, unlocked, and protected against tampering
    public function test_26_minified_module_remains_available_and_verified()
    {
        $instId = InstallationService::getInstallationId();
        $this->createEncryptedModuleState('Commerce');

        $activeLicense = $this->authoritySign([
            'license_id'      => 'LIC-2026-MINIFIED-CHECK',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['commerce'],
        ]);

        $actRes = ActivationService::activate($activeLicense);
        $this->assertTrue($actRes['success']);

        // Minify the module's controller
        $ctrlPath = app_path('Modules/Commerce/Controllers/Commerce.php');
        $minifier = app(\App\Services\Dapcode\CodeMinifierService::class);
        $miniRes = $minifier->minifyFile($ctrlPath);
        $this->assertTrue($miniRes['success']);

        // Must still be UNLOCKED and accessible
        $this->assertEquals('UNLOCKED', ModuleEncryptionService::getModuleStatus('Commerce'));
        $this->assertTrue(ModuleEncryptionService::isModuleAvailable('Commerce'));
        $this->get('/commerce')->assertStatus(200);

        // Tamper test: modifying the minified file must flag TAMPERED and 403
        File::put($ctrlPath, File::get($ctrlPath) . ' // tamper');
        $this->assertEquals('TAMPERED', ModuleEncryptionService::getModuleStatus('Commerce'));
        $this->assertFalse(ModuleEncryptionService::isModuleAvailable('Commerce'));
        $this->get('/commerce')->assertStatus(403);
    }

    // SCENARIO 1: License -> Minify
    // 1. Activate license Dashboard
    // 2. Dashboard can be opened
    // 3. Minify Dashboard
    // 4. License status remains ACTIVE
    // 5. Dashboard can still be opened
    public function test_27_scenario_1_license_minify_access_allowed()
    {
        $instId = InstallationService::getInstallationId();
        $this->createEncryptedModuleState('Commerce');

        $license = $this->authoritySign([
            'license_id'      => 'LIC-2026-SCENARIO-1',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['commerce'],
        ]);

        $actRes = ActivationService::activate($license);
        $this->assertTrue($actRes['success']);
        $this->assertEquals('ACTIVE', LicenseGuard::getStatus());
        $this->get('/commerce')->assertStatus(200);

        // Minify module files
        $ctrlPath = app_path('Modules/Commerce/Controllers/Commerce.php');
        $minifier = app(\App\Services\Dapcode\CodeMinifierService::class);
        $miniRes = $minifier->minifyFile($ctrlPath);
        $this->assertTrue($miniRes['success']);

        // Check source state is minified
        $this->assertEquals('minified', \App\Services\Dapcode\LicenseAuditLogger::detectSourceState($ctrlPath));

        // License must remain ACTIVE and module access ALLOWED
        $this->assertEquals('ACTIVE', LicenseGuard::getStatus());
        $this->assertEquals('UNLOCKED', ModuleEncryptionService::getModuleStatus('Commerce'));
        $this->assertTrue(ModuleEncryptionService::isModuleAvailable('Commerce'));
        $this->get('/commerce')->assertStatus(200);
    }

    // SCENARIO 2: License -> Minify -> Restart (Cache Clear)
    // 1. Activate license
    // 2. Minify Dashboard
    // 3. Restart application / clear cache
    // 4. Dashboard remains accessible, license remains ACTIVE
    public function test_28_scenario_2_license_minify_restart_cache_clear_access_allowed()
    {
        $instId = InstallationService::getInstallationId();
        $this->createEncryptedModuleState('Commerce');

        $license = $this->authoritySign([
            'license_id'      => 'LIC-2026-SCENARIO-2',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['commerce'],
        ]);

        $actRes = ActivationService::activate($license);
        $this->assertTrue($actRes['success']);

        // Minify controller
        $ctrlPath = app_path('Modules/Commerce/Controllers/Commerce.php');
        $minifier = app(\App\Services\Dapcode\CodeMinifierService::class);
        $minifier->minifyFile($ctrlPath);

        // Simulate application restart: clear all caches
        LicenseGuard::clearCache();
        IntegrityService::clearCache();
        if (function_exists('cache')) {
            try { cache()->flush(); } catch (\Throwable $e) {}
        }

        // Must still be ACTIVE and accessible
        $this->assertEquals('ACTIVE', LicenseGuard::getStatus());
        $this->assertTrue(ModuleEncryptionService::isModuleAvailable('Commerce'));
        $this->get('/commerce')->assertStatus(200);
    }

    // SCENARIO 3: License -> Minify -> Reactivate License
    // 1. Activate Dashboard license
    // 2. Minify Dashboard
    // 3. Re-run activation process
    // 4. Source remains MINIFIED (no auto-unminify)
    // 5. License remains ACTIVE
    public function test_29_scenario_3_license_minify_reactivate_preserves_minified_state()
    {
        $instId = InstallationService::getInstallationId();
        $this->createEncryptedModuleState('Commerce');

        $license = $this->authoritySign([
            'license_id'      => 'LIC-2026-SCENARIO-3',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['commerce'],
        ]);

        $actRes = ActivationService::activate($license);
        $this->assertTrue($actRes['success']);

        // Minify
        $ctrlPath = app_path('Modules/Commerce/Controllers/Commerce.php');
        $minifier = app(\App\Services\Dapcode\CodeMinifierService::class);
        $minifier->minifyFile($ctrlPath);

        $this->assertEquals('minified', \App\Services\Dapcode\LicenseAuditLogger::detectSourceState($ctrlPath));

        // Reactivate license
        $reactRes = ActivationService::activate($license);
        $this->assertTrue($reactRes['success']);

        // BUG 2 VERIFICATION: Source code MUST remain minified (NO auto-unminify)
        $this->assertEquals('minified', \App\Services\Dapcode\LicenseAuditLogger::detectSourceState($ctrlPath));
        $this->assertEquals('ACTIVE', LicenseGuard::getStatus());
        $this->get('/commerce')->assertStatus(200);
    }

    // SCENARIO 4: License -> Unminify
    // 1. Activate Dashboard license
    // 2. Ensure source is MINIFIED
    // 3. Run explicit UNMINIFY process
    // 4. Source = UNMINIFIED, License = ACTIVE, Access = ALLOWED (No auto-revoke)
    public function test_30_scenario_4_license_unminify_preserves_active_license_and_access()
    {
        $instId = InstallationService::getInstallationId();
        $this->createEncryptedModuleState('Commerce');

        $license = $this->authoritySign([
            'license_id'      => 'LIC-2026-SCENARIO-4',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['commerce'],
        ]);

        $actRes = ActivationService::activate($license);
        $this->assertTrue($actRes['success']);

        // Minify
        $ctrlPath = app_path('Modules/Commerce/Controllers/Commerce.php');
        $minifier = app(\App\Services\Dapcode\CodeMinifierService::class);
        $minifier->minifyFile($ctrlPath);
        $this->assertEquals('minified', \App\Services\Dapcode\LicenseAuditLogger::detectSourceState($ctrlPath));

        // Explicit unminify
        $formatter = app(\App\Services\Dapcode\CodeFormatterService::class);
        $unminiRes = $formatter->unminifyFile($ctrlPath);
        $this->assertTrue($unminiRes['success']);

        // BUG 3 VERIFICATION: Source is unminified, license NOT revoked
        $this->assertEquals('original', \App\Services\Dapcode\LicenseAuditLogger::detectSourceState($ctrlPath));
        $this->assertEquals('ACTIVE', LicenseGuard::getStatus());
        $this->assertTrue(ModuleEncryptionService::isModuleAvailable('Commerce'));
        $this->get('/commerce')->assertStatus(200);
    }

    // SCENARIO 5: Revoke License
    // 1. License is ACTIVE
    // 2. Run explicit REVOKE via Signed Revocation Token
    // 3. License = REVOKED, Module Access = DENIED regardless of source state
    public function test_31_scenario_5_explicit_revoke_revokes_license_regardless_of_minification()
    {
        $instId = InstallationService::getInstallationId();
        $this->createEncryptedModuleState('Commerce');

        $licenseId = 'LIC-2026-SCENARIO-5';
        $license = $this->authoritySign([
            'license_id'      => $licenseId,
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['commerce'],
        ]);

        $actRes = ActivationService::activate($license);
        $this->assertTrue($actRes['success']);

        // Minify
        $ctrlPath = app_path('Modules/Commerce/Controllers/Commerce.php');
        $minifier = app(\App\Services\Dapcode\CodeMinifierService::class);
        $minifier->minifyFile($ctrlPath);
        $this->get('/commerce')->assertStatus(200);

        // Explicit revocation
        $revocationToken = $this->authorityRevoke($licenseId, $instId, 'Explicit Test Revocation');
        $deactRes = ActivationService::deactivate($revocationToken);
        $this->assertTrue($deactRes['success']);

        // Access must be DENIED and status must be REVOKED
        $this->assertEquals('REVOKED', LicenseGuard::getStatus());
        $this->get('/commerce')->assertStatus(403);
    }

    public function test_32_portfolio_files_grouped_with_routes_not_aegisguard()
    {
        $minifier = app(\App\Services\Dapcode\CodeMinifierService::class);

        $portfolioController = app_path('Http/Controllers/PortfolioController.php');
        $portfolioBlade = resource_path('views/portfolio.blade.php');

        // 1. Verify getTargetFiles('routes') includes routes + portfolio files
        $routesFiles = $minifier->getTargetFiles('routes');
        $this->assertContains(realpath($portfolioController) ?: $portfolioController, $routesFiles);
        $this->assertContains(realpath($portfolioBlade) ?: $portfolioBlade, $routesFiles);
        $this->assertContains(realpath(base_path('routes/web.php')) ?: base_path('routes/web.php'), $routesFiles);
        $this->assertContains(realpath(base_path('routes/api.php')) ?: base_path('routes/api.php'), $routesFiles);

        // 2. Verify getTargetFiles('aegisguard') does NOT include portfolio files
        $aegisFiles = $minifier->getTargetFiles('aegisguard');
        $this->assertNotContains(realpath($portfolioController) ?: $portfolioController, $aegisFiles);
        $this->assertNotContains(realpath($portfolioBlade) ?: $portfolioBlade, $aegisFiles);

        // 3. Verify artisan command code:status routes includes portfolio
        \Illuminate\Support\Facades\Artisan::call('code:status', ['target' => 'routes']);
        $routesOutput = \Illuminate\Support\Facades\Artisan::output();
        $this->assertStringContainsString('PortfolioController.php', $routesOutput);
        $this->assertStringContainsString('portfolio.blade.php', $routesOutput);

        // 4. Verify artisan command code:status aegisguard does not include portfolio
        \Illuminate\Support\Facades\Artisan::call('code:status', ['target' => 'aegisguard']);
        $aegisOutput = \Illuminate\Support\Facades\Artisan::output();
        $this->assertStringNotContainsString('PortfolioController.php', $aegisOutput);
        $this->assertStringNotContainsString('portfolio.blade.php', $aegisOutput);
    }

    public function test_33_encrypted_enc_files_grouped_with_aegisguard()
    {
        $minifier = app(\App\Services\Dapcode\CodeMinifierService::class);

        $dashEnc = app_path('Modules/Dashboard/Encrypted/Controllers/Dashboard.php.enc');
        $dashModelEnc = app_path('Modules/Dashboard/Encrypted/Models/Dashboard.php.enc');

        // 1. Verify getTargetFiles('aegisguard') includes .enc files
        $aegisFiles = $minifier->getTargetFiles('aegisguard');
        $this->assertContains(realpath($dashEnc) ?: $dashEnc, $aegisFiles);
        $this->assertContains(realpath($dashModelEnc) ?: $dashModelEnc, $aegisFiles);

        // 2. Verify getTargetFiles('modules') does NOT include .enc files
        $moduleFiles = $minifier->getTargetFiles('modules');
        $this->assertNotContains(realpath($dashEnc) ?: $dashEnc, $moduleFiles);
        $this->assertNotContains(realpath($dashModelEnc) ?: $dashModelEnc, $moduleFiles);

        // 3. Verify artisan command code:status aegisguard includes .enc with type ENC
        \Illuminate\Support\Facades\Artisan::call('code:status', ['target' => 'aegisguard']);
        $aegisOutput = \Illuminate\Support\Facades\Artisan::output();
        $this->assertStringContainsString('Dashboard.php.enc', $aegisOutput);
        $this->assertStringContainsString('ENC', $aegisOutput);
    }

    public function test_34_helpers_middlewares_and_kernel_grouped_with_routes_and_all()
    {
        $minifier = app(\App\Services\Dapcode\CodeMinifierService::class);

        $kernel = app_path('Http/Kernel.php');
        $authMiddleware = app_path('Http/Middleware/Authenticate.php');
        $csrfMiddleware = app_path('Http/Middleware/VerifyCsrfToken.php');
        $hmvcHelper = app_path('Helpers/hmvc.php');
        $viteHelper = app_path('Services/Vite/ViteHelper.php');

        // 1. Verify getTargetFiles('routes') includes all of them
        $routesFiles = $minifier->getTargetFiles('routes');
        $this->assertContains(realpath($kernel) ?: $kernel, $routesFiles);
        $this->assertContains(realpath($authMiddleware) ?: $authMiddleware, $routesFiles);
        $this->assertContains(realpath($csrfMiddleware) ?: $csrfMiddleware, $routesFiles);
        $this->assertContains(realpath($hmvcHelper) ?: $hmvcHelper, $routesFiles);
        $this->assertContains(realpath($viteHelper) ?: $viteHelper, $routesFiles);

        // 2. Verify getTargetFiles('all') includes all of them
        $allFiles = $minifier->getTargetFiles('all');
        $this->assertContains(realpath($kernel) ?: $kernel, $allFiles);
        $this->assertContains(realpath($authMiddleware) ?: $authMiddleware, $allFiles);
        $this->assertContains(realpath($csrfMiddleware) ?: $csrfMiddleware, $allFiles);
        $this->assertContains(realpath($hmvcHelper) ?: $hmvcHelper, $allFiles);
        $this->assertContains(realpath($viteHelper) ?: $viteHelper, $allFiles);

        // 3. Verify individual target resolution
        $middlewareFiles = $minifier->getTargetFiles('middlewares');
        $this->assertContains(realpath($kernel) ?: $kernel, $middlewareFiles);
        $this->assertContains(realpath($authMiddleware) ?: $authMiddleware, $middlewareFiles);

        $helperFiles = $minifier->getTargetFiles('helpers');
        $this->assertContains(realpath($hmvcHelper) ?: $hmvcHelper, $helperFiles);
        $this->assertContains(realpath($viteHelper) ?: $viteHelper, $helperFiles);

        $kernelFiles = $minifier->getTargetFiles('kernel');
        $this->assertContains(realpath($kernel) ?: $kernel, $kernelFiles);
    }
}


