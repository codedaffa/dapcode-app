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

    protected function setUp(): void
    {
        parent::setUp();
        if (file_exists($this->privKeyPath)) {
            $this->authorityPrivateKey = file_get_contents($this->privKeyPath);
        }

        // Backup Commerce & Career controllers and models
        $modules = ['Commerce', 'Career', 'Project', 'Research'];
        foreach ($modules as $mod) {
            $ctrl = app_path("Modules/{$mod}/Controllers/{$mod}.php");
            if (!isset(self::$originalControllers[$mod]) && File::exists($ctrl)) {
                self::$originalControllers[$mod] = File::get($ctrl);
            }
            $model = app_path("Modules/{$mod}/Models/{$mod}.php");
            if (!isset(self::$originalModels[$mod]) && File::exists($model)) {
                self::$originalModels[$mod] = File::get($model);
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
        IntegrityService::recordCoreFilesManifest();
        LicenseGuard::clearCache();
        parent::tearDown();
    }

    protected function restorePlaintextFiles(): void
    {
        foreach (self::$originalControllers as $mod => $content) {
            $ctrlPath = app_path("Modules/{$mod}/Controllers/{$mod}.php");
            File::put($ctrlPath, $content);
            $encDir = app_path("Modules/{$mod}/Encrypted");
            if (File::isDirectory($encDir)) {
                File::deleteDirectory($encDir);
            }
        }

        foreach (self::$originalModels as $mod => $content) {
            $modelPath = app_path("Modules/{$mod}/Models/{$mod}.php");
            File::put($modelPath, $content);
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
        if (!File::exists($ctrlPath) && isset(self::$originalControllers[$modStudly])) {
            File::put($ctrlPath, self::$originalControllers[$modStudly]);
        }
        $modelPath = app_path("Modules/{$modStudly}/Models/{$modStudly}.php");
        if (!File::exists($modelPath) && isset(self::$originalModels[$modStudly])) {
            File::put($modelPath, self::$originalModels[$modStudly]);
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
        $this->assertFileExists(app_path('Modules/Commerce/Encrypted/manifest.json'));
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
        $manifestPath = app_path('Modules/Commerce/Encrypted/manifest.json');

        $manifest = json_decode(File::get($manifestPath), true);
        $manifest['salt'] = bin2hex(random_bytes(16)); // Tamper salt
        File::put($manifestPath, json_encode($manifest));

        $res = ModuleEncryptionService::unlockModule('Commerce', $license);
        $this->assertFalse($res['success']);
    }

    // 18. Checksum mismatch fails
    public function test_18_checksum_mismatch_fails()
    {
        $license = $this->createEncryptedModuleState('Commerce');
        $manifestPath = app_path('Modules/Commerce/Encrypted/manifest.json');

        $manifest = json_decode(File::get($manifestPath), true);
        $manifest['files'][0]['sha256'] = hash('sha256', 'tampered-payload-expectation');
        File::put($manifestPath, json_encode($manifest));

        $res = ModuleEncryptionService::unlockModule('Commerce', $license);
        $this->assertFalse($res['success']);
    }

    // 19. Path traversal is rejected
    public function test_19_path_traversal_is_rejected()
    {
        $license = $this->createEncryptedModuleState('Commerce');
        $manifestPath = app_path('Modules/Commerce/Encrypted/manifest.json');

        $manifest = json_decode(File::get($manifestPath), true);
        $manifest['files'][0]['path'] = '../../../../public/hacked.php';
        File::put($manifestPath, json_encode($manifest));

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
}
