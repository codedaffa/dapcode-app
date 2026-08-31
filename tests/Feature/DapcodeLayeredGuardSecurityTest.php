<?php

namespace Tests\Feature;

use App\Http\Controllers\Core\DashboardControllers;
use App\Http\Middleware\DapcodeLicenseMiddleware;
use App\Modules\Dashboard\Controllers\Dashboard;
use App\Services\Dapcode\ActivationService;
use App\Services\Dapcode\InstallationService;
use App\Services\Dapcode\IntegrityService;
use App\Services\Dapcode\LicenseGuard;
use App\Services\Dapcode\LicenseVerifier;
use App\Services\Dapcode\ModuleEncryptionService;
use App\Services\HMVC\HMVC;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DapcodeLayeredGuardSecurityTest extends TestCase
{
    /** @var string */
    protected $privKeyPath = 'C:\Users\po\.gemini\antigravity-ide\brain\990a2152-70dc-4fd4-a1f5-79df37e16c3c\authority_private_key.pem';

    /** @var string */
    protected $authorityPrivateKey = '';

    protected function setUp(): void
    {
        parent::setUp();
        if (file_exists($this->privKeyPath)) {
            $this->authorityPrivateKey = file_get_contents($this->privKeyPath);
        }
        $this->resetLicenseFiles();
        IntegrityService::recordCoreFilesManifest();
        LicenseGuard::clearCache();
    }

    protected function tearDown(): void
    {
        $this->resetLicenseFiles();
        IntegrityService::recordCoreFilesManifest();
        LicenseGuard::clearCache();
        parent::tearDown();
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
        if (empty($payload['auth_token']) && isset($payload['license_id'], $payload['installation_id'])) {
            $payload['auth_token'] = LicenseVerifier::generateAuthToken((string) $payload['license_id'], (string) $payload['installation_id']);
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

    protected function authorityRevoke(string $licenseId, string $installationId, string $reason = 'Manual Revocation'): array
    {
        $payload = [
            'action'          => 'REVOKE',
            'license_id'      => $licenseId,
            'installation_id' => $installationId,
            'revoked_at'      => date('c'),
            'reason'          => $reason,
            'auth_token'      => LicenseVerifier::generateAuthToken($licenseId, $installationId, 'REVOKE'),
        ];
        $clean = $payload;
        ksort($clean);
        $canonical = json_encode($clean, JSON_UNESCAPED_SLASHES);
        $binarySig = '';
        openssl_sign($canonical, $binarySig, $this->authorityPrivateKey, OPENSSL_ALGO_SHA256);
        $payload['signature'] = base64_encode($binarySig);
        return $payload;
    }

    /** @test */
    public function test_1_normal_valid_license_allows_protected_module()
    {
        if (empty($this->authorityPrivateKey)) $this->markTestSkipped('Authority private key not found');

        $instId = InstallationService::getInstallationId();
        $license = $this->authoritySign([
            'license_id'      => 'LIC-LAYER-001',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['*'],
        ]);

        $result = ActivationService::activate($license);
        $this->assertTrue($result['success']);

        $this->get('/dashboard')->assertStatus(200);
        $this->get('/setting')->assertStatus(200);
    }

    /** @test */
    public function test_2_missing_license_denies_protected_module()
    {
        $this->resetLicenseFiles();

        $this->get('/dashboard')->assertStatus(403);
        $this->get('/setting')->assertStatus(403);
    }

    /** @test */
    public function test_3_middleware_bypass_simulation_is_blocked_by_layer_2_and_3()
    {
        $this->resetLicenseFiles();

        // Simulate Layer 1 Middleware completely bypassed by directly running HMVC dispatcher (Layer 2)
        $hmvc = new HMVC();
        $request = Request::create('/dashboard', 'GET');

        try {
            $hmvc->dispatch($request, 'dashboard');
            $this->fail('HMVC Dispatcher (Layer 2) should have thrown HttpResponseException 403');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
            $this->assertEquals(403, $response->getStatusCode());
        }
    }

    /** @test */
    public function test_4_controller_direct_access_is_blocked_by_layer_3()
    {
        $this->resetLicenseFiles();

        // Simulate direct controller instantiation bypassing both middleware and HMVC router
        try {
            new Dashboard();
            $this->fail('Controller Constructor (Layer 3) should have thrown HttpResponseException 403');
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
            $this->assertEquals(403, $response->getStatusCode());
        }
    }

    /** @test */
    public function test_5_invalid_signature_locks_module_access()
    {
        $instId = InstallationService::getInstallationId();
        $fakeLicense = [
            'license_id'      => 'LIC-FAKE-LAYER',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['*'],
            'signature'       => base64_encode(random_bytes(256)),
        ];

        ActivationService::activate($fakeLicense);

        $this->get('/dashboard')->assertStatus(403);
    }

    /** @test */
    public function test_6_tampered_core_file_causes_layer_5_integrity_failure()
    {
        if (empty($this->authorityPrivateKey)) $this->markTestSkipped('Authority private key not found');

        // 1. Activate valid license first
        $instId = InstallationService::getInstallationId();
        $license = $this->authoritySign([
            'license_id'      => 'LIC-INTEGRITY-001',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['*'],
        ]);
        ActivationService::activate($license);
        $this->get('/dashboard')->assertStatus(200);

        // 2. Corrupt manifest to simulate modified core file
        $manifestPath = config('dapcode.files.integrity_manifest', storage_path('app/dapcode/.integrity-manifest'));
        $manifestData = json_decode(File::get($manifestPath), true);
        $manifestData['files']['middleware']['hash'] = hash('sha256', 'malicious_modified_middleware_content');
        File::put($manifestPath, json_encode($manifestData));

        IntegrityService::clearCache();

        // 3. Status must now be INTEGRITY_FAILED and access forbidden (403)
        $this->assertEquals('INTEGRITY_FAILED', LicenseGuard::getStatus());
        $this->get('/dashboard')->assertStatus(403);
    }

    /** @test */
    public function test_7_valid_license_after_restore_restores_access()
    {
        if (empty($this->authorityPrivateKey)) $this->markTestSkipped('Authority private key not found');

        $instId = InstallationService::getInstallationId();
        $license = $this->authoritySign([
            'license_id'      => 'LIC-RESTORE-001',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['*'],
        ]);
        ActivationService::activate($license);

        // Restore manifest and clear cache
        IntegrityService::recordCoreFilesManifest();
        LicenseGuard::clearCache();

        $this->get('/dashboard')->assertStatus(200);
        $this->assertEquals('ACTIVE', LicenseGuard::getStatus());
    }

    /** @test */
    public function test_8_json_request_returns_clean_json_403()
    {
        $this->resetLicenseFiles();

        $response = $this->getJson('/dashboard');
        $response->assertStatus(403);
        $response->assertJson([
            'status' => 'forbidden',
            'code'   => 403,
        ]);
    }

    /** @test */
    public function test_9_hierarchical_hmvc_run_is_blocked_without_valid_license()
    {
        $this->resetLicenseFiles();

        try {
            HMVC::run('Dashboard@index');
            $this->fail('HMVC::run should have thrown HttpResponseException 403');
        } catch (HttpResponseException $e) {
            $this->assertEquals(403, $e->getResponse()->getStatusCode());
        }
    }

    /** @test */
    public function test_10_unauthorized_module_is_blocked_even_with_valid_license()
    {
        if (empty($this->authorityPrivateKey)) $this->markTestSkipped('Authority private key not found');

        $instId = InstallationService::getInstallationId();
        $license = $this->authoritySign([
            'license_id'      => 'LIC-COMMERCE-ONLY-01',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['commerce'],
        ]);

        ActivationService::activate($license);

        // Authorized module works
        $this->get('/commerce')->assertStatus(200);

        // Unauthorized modules are strictly denied (403)
        $this->get('/project')->assertStatus(403);
        $this->get('/career')->assertStatus(403);
        $this->get('/setting')->assertStatus(403);
    }

    /** @test */
    public function test_11_revoked_license_blocks_all_protected_execution_paths()
    {
        if (empty($this->authorityPrivateKey)) $this->markTestSkipped('Authority private key not found');

        $instId = InstallationService::getInstallationId();
        $license = $this->authoritySign([
            'license_id'      => 'LIC-TO-REVOKE-01',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['*'],
        ]);

        ActivationService::activate($license);
        $this->get('/dashboard')->assertStatus(200);

        // Revoke license
        $token = $this->authorityRevoke($license['license_id'], $instId);
        ActivationService::deactivate($token);

        // All protected routes must be blocked
        $this->get('/dashboard')->assertStatus(403);
        $this->get('/setting')->assertStatus(403);
        $this->get('/commerce')->assertStatus(403);
    }

    /** @test */
    public function test_12_canonical_module_resolver_rejects_traversal_and_obfuscation()
    {
        $this->assertEquals('commerce', HMVC::resolveCanonicalModuleName('/commerce'));
        $this->assertEquals('commerce', HMVC::resolveCanonicalModuleName('/Commerce/'));
        $this->assertEquals('commerce', HMVC::resolveCanonicalModuleName('%63ommerce'));
        $this->assertEquals('commerce', HMVC::resolveCanonicalModuleName('//commerce'));
        $this->assertNull(HMVC::resolveCanonicalModuleName('/commerce/../commerce'));
        $this->assertNull(HMVC::resolveCanonicalModuleName('..'));
        $this->assertNull(HMVC::resolveCanonicalModuleName(''));
    }
}
