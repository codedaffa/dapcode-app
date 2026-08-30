<?php

namespace Tests\Feature;

use App\Services\Dapcode\ActivationService;
use App\Services\Dapcode\InstallationService;
use App\Services\Dapcode\LicenseGuard;
use App\Services\Dapcode\LicenseVerifier;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DapcodeLicenseSecurityTest extends TestCase
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
    }

    protected function tearDown(): void
    {
        $this->resetLicenseFiles();
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

    protected function authorityRevoke(string $licenseId, string $installationId, string $reason = 'Manual Revocation', ?array $revokedModules = null): array
    {
        $payload = [
            'action'          => 'REVOKE',
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

    /** @test */
    public function test_1_fresh_clone_denies_all_routes_except_activate()
    {
        $this->resetLicenseFiles();

        // ONLY /dapcode/activate is accessible (200)
        $this->get('/dapcode/activate')->assertStatus(200);

        // All other application routes are locked (403)
        $this->get('/')->assertStatus(403);
        $this->get('/dashboard')->assertStatus(403);
        $this->get('/profile')->assertStatus(403);
        $this->get('/education')->assertStatus(403);
        $this->get('/commerce')->assertStatus(403);
        $this->get('/project')->assertStatus(403);
        $this->get('/career')->assertStatus(403);
        $this->get('/research')->assertStatus(403);
        $this->get('/setting')->assertStatus(403);
    }

    /** @test */
    public function test_2_active_wildcard_license_unlocks_all_routes()
    {
        if (empty($this->authorityPrivateKey)) $this->markTestSkipped('Authority private key not found');

        $instId = InstallationService::getInstallationId();
        $license = $this->authoritySign([
            'license_id'      => 'LIC-TEST-ALL-001',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['*'],
        ]);

        $result = ActivationService::activate($license);
        $this->assertTrue($result['success']);

        $this->get('/dapcode/activate')->assertStatus(200);
        $this->get('/')->assertStatus(200);
        $this->get('/dashboard')->assertStatus(200);
        $this->get('/profile')->assertStatus(200);
        $this->get('/education')->assertStatus(200);
        $this->get('/commerce')->assertStatus(200);
        $this->get('/project')->assertStatus(200);
        $this->get('/career')->assertStatus(200);
        $this->get('/research')->assertStatus(200);
        $this->get('/setting')->assertStatus(200);
    }

    /** @test */
    public function test_3_active_single_module_license_allows_homepage_and_allowed_module_only()
    {
        if (empty($this->authorityPrivateKey)) $this->markTestSkipped('Authority private key not found');

        $instId = InstallationService::getInstallationId();
        $license = $this->authoritySign([
            'license_id'      => 'LIC-COMMERCE-ONLY',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['commerce'],
        ]);

        ActivationService::activate($license);

        $this->get('/dapcode/activate')->assertStatus(200);
        $this->get('/')->assertStatus(200);
        $this->get('/commerce')->assertStatus(200);

        // Other modules are forbidden (403)
        $this->get('/project')->assertStatus(403);
        $this->get('/career')->assertStatus(403);
        $this->get('/research')->assertStatus(403);
        $this->get('/setting')->assertStatus(403);
    }

    /** @test */
    public function test_4_active_multiple_module_license_allows_only_permitted_modules()
    {
        if (empty($this->authorityPrivateKey)) $this->markTestSkipped('Authority private key not found');

        $instId = InstallationService::getInstallationId();
        $license = $this->authoritySign([
            'license_id'      => 'LIC-COMMERCE-PROJECT',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['commerce', 'project'],
        ]);

        ActivationService::activate($license);

        $this->get('/dapcode/activate')->assertStatus(200);
        $this->get('/')->assertStatus(200);
        $this->get('/commerce')->assertStatus(200);
        $this->get('/project')->assertStatus(200);

        $this->get('/career')->assertStatus(403);
        $this->get('/research')->assertStatus(403);
        $this->get('/setting')->assertStatus(403);
    }

    /** @test */
    public function test_5_revoked_license_locks_all_routes_except_activate()
    {
        if (empty($this->authorityPrivateKey)) $this->markTestSkipped('Authority private key not found');

        $instId = InstallationService::getInstallationId();
        $license = $this->authoritySign([
            'license_id'      => 'LIC-TO-REVOKE',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['*'],
        ]);
        ActivationService::activate($license);
        $this->get('/')->assertStatus(200);
        $this->get('/commerce')->assertStatus(200);

        $revokeToken = $this->authorityRevoke($license['license_id'], $instId, 'Contract Termination');
        $deact = ActivationService::deactivate($revokeToken);
        $this->assertTrue($deact['success']);

        $this->get('/dapcode/activate')->assertStatus(200);
        $this->get('/')->assertStatus(403);
        $this->get('/dashboard')->assertStatus(403);
        $this->get('/profile')->assertStatus(403);
        $this->get('/commerce')->assertStatus(403);
        $this->get('/project')->assertStatus(403);
        $this->get('/career')->assertStatus(403);
    }

    /** @test */
    public function test_6_expired_license_locks_all_routes_except_activate()
    {
        if (empty($this->authorityPrivateKey)) $this->markTestSkipped('Authority private key not found');

        $instId = InstallationService::getInstallationId();
        $expired = $this->authoritySign([
            'license_id'      => 'LIC-EXP-001',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c', strtotime('-2 years')),
            'expires_at'      => date('c', strtotime('-1 day')),
            'modules'         => ['*'],
        ]);

        ActivationService::activate($expired);

        $this->get('/dapcode/activate')->assertStatus(200);
        $this->get('/')->assertStatus(403);
        $this->get('/dashboard')->assertStatus(403);
        $this->get('/commerce')->assertStatus(403);
    }

    /** @test */
    public function test_7_invalid_signature_locks_all_routes_except_activate()
    {
        $instId = InstallationService::getInstallationId();
        $fakeLicense = [
            'license_id'      => 'LIC-TEST-FAKE',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['*'],
            'signature'       => base64_encode(random_bytes(256)),
        ];

        ActivationService::activate($fakeLicense);

        $this->get('/dapcode/activate')->assertStatus(200);
        $this->get('/')->assertStatus(403);
        $this->get('/commerce')->assertStatus(403);
    }

    /** @test */
    public function test_8_corrupted_or_missing_license_fails_closed()
    {
        $this->resetLicenseFiles();

        $this->get('/dapcode/activate')->assertStatus(200);
        $this->get('/')->assertStatus(403);
        $this->get('/commerce')->assertStatus(403);

        $licensePath = config('dapcode.files.license');
        File::put($licensePath, '{corrupted-json}');

        $this->get('/dapcode/activate')->assertStatus(200);
        $this->get('/')->assertStatus(403);
        $this->get('/commerce')->assertStatus(403);
    }

    /** @test */
    public function test_9_tampering_status_fails_verification()
    {
        if (empty($this->authorityPrivateKey)) $this->markTestSkipped('Authority private key not found');
        $instId = InstallationService::getInstallationId();
        $revoked = $this->authoritySign([
            'license_id'      => 'LIC-REVOKED-001',
            'installation_id' => $instId,
            'status'          => 'REVOKED',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['*'],
        ]);
        $revoked['status'] = 'ACTIVE';
        $v = LicenseVerifier::verify($revoked);
        $this->assertFalse($v['valid']);
    }

    /** @test */
    public function test_10_tampering_expiration_fails_verification()
    {
        if (empty($this->authorityPrivateKey)) $this->markTestSkipped('Authority private key not found');
        $instId = InstallationService::getInstallationId();
        $license = $this->authoritySign([
            'license_id'      => 'LIC-ORIGINAL-001',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['*'],
        ]);
        $license['expires_at'] = '2099-12-31T23:59:59+00:00';
        $v = LicenseVerifier::verify($license);
        $this->assertFalse($v['valid']);
    }

    /** @test */
    public function test_11_tampering_modules_fails_verification()
    {
        if (empty($this->authorityPrivateKey)) $this->markTestSkipped('Authority private key not found');
        $instId = InstallationService::getInstallationId();
        $license = $this->authoritySign([
            'license_id'      => 'LIC-LIMITED-001',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['commerce'],
        ]);
        $license['modules'] = ['*'];
        $v = LicenseVerifier::verify($license);
        $this->assertFalse($v['valid']);
    }

    /** @test */
    public function test_12_tampering_installation_id_fails_verification()
    {
        if (empty($this->authorityPrivateKey)) $this->markTestSkipped('Authority private key not found');
        $license = $this->authoritySign([
            'license_id'      => 'LIC-ORIGINAL-001',
            'installation_id' => 'DAP-OTHER-MACHINE-888',
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['*'],
        ]);
        $v = LicenseVerifier::verify($license);
        $this->assertFalse($v['valid']);
    }

    /** @test */
    public function test_13_fake_request_headers_cannot_bypass_license()
    {
        $this->resetLicenseFiles();
        $response = $this->withHeaders([
            'X-License-Bypass' => '1',
            'X-Licensed'       => 'true',
            'Authorization'    => 'Bearer master-secret',
        ])->get('/commerce');
        $response->assertStatus(403);

        $this->withHeaders(['X-License-Bypass' => '1'])->get('/')->assertStatus(403);
    }

    /** @test */
    public function test_14_fake_cookies_cannot_bypass_license()
    {
        $this->resetLicenseFiles();
        $response = $this->withCookies([
            'licensed'    => 'true',
            'dapcode_act' => 'ACTIVE',
        ])->get('/commerce');
        $response->assertStatus(403);

        $this->withCookies(['licensed' => 'true'])->get('/')->assertStatus(403);
    }

    /** @test */
    public function test_15_query_string_parameters_cannot_bypass_license()
    {
        $this->resetLicenseFiles();
        $response = $this->get('/commerce?licensed=1&bypass=true&key=admin');
        $response->assertStatus(403);

        $this->get('/?licensed=1&bypass=true')->assertStatus(403);
    }

    /** @test */
    public function test_16_app_env_or_debug_mode_cannot_bypass_license()
    {
        $this->resetLicenseFiles();
        Config::set('app.env', 'local');
        Config::set('app.debug', true);
        $this->get('/commerce')->assertStatus(403);
        $this->get('/')->assertStatus(403);

        Config::set('app.env', 'development');
        Config::set('app.debug', false);
        $this->get('/commerce')->assertStatus(403);
        $this->get('/')->assertStatus(403);
    }

    /** @test */
    public function test_17_api_endpoint_returns_json_403()
    {
        $this->resetLicenseFiles();
        $response = $this->getJson('/commerce');
        $response->assertStatus(403);
        $response->assertJson(['status' => 'forbidden', 'code' => 403]);
    }

    /** @test */
    public function test_18_granular_module_revocation()
    {
        if (empty($this->authorityPrivateKey)) $this->markTestSkipped('Authority private key not found');
        $instId = InstallationService::getInstallationId();
        $license = $this->authoritySign([
            'license_id'      => 'LIC-GRANULAR-TEST',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['commerce', 'career', 'project'],
        ]);
        ActivationService::activate($license);

        $this->get('/')->assertStatus(200);
        $this->get('/commerce')->assertStatus(200);
        $this->get('/career')->assertStatus(200);
        $this->get('/project')->assertStatus(200);

        // Revoke ONLY the commerce module
        $granularRevokeToken = $this->authorityRevoke($license['license_id'], $instId, 'Revoke Commerce Only', ['commerce']);
        $deact = ActivationService::deactivate($granularRevokeToken);
        $this->assertTrue($deact['success']);

        // Commerce should now be 403, while homepage, career and project remain 200
        $this->get('/')->assertStatus(200);
        $this->get('/commerce')->assertStatus(403);
        $this->get('/career')->assertStatus(200);
        $this->get('/project')->assertStatus(200);
    }

    /** @test */
    public function test_19_reactivation_after_revocation_restores_access()
    {
        if (empty($this->authorityPrivateKey)) $this->markTestSkipped('Authority private key not found');
        $instId = InstallationService::getInstallationId();

        // 1. Initial Activation
        $license1 = $this->authoritySign([
            'license_id'      => 'LIC-PHASE-1',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['*'],
        ]);
        ActivationService::activate($license1);
        $this->get('/')->assertStatus(200);

        // 2. Full Revocation
        $revokeToken = $this->authorityRevoke('LIC-PHASE-1', $instId, 'Revocation Phase');
        ActivationService::deactivate($revokeToken);
        $this->get('/')->assertStatus(403);

        // 3. Reactivation with new license
        $license2 = $this->authoritySign([
            'license_id'      => 'LIC-PHASE-2',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['commerce'],
        ]);
        $react = ActivationService::activate($license2);
        $this->assertTrue($react['success']);

        $this->get('/')->assertStatus(200);
        $this->get('/commerce')->assertStatus(200);
        $this->get('/project')->assertStatus(403);
    }

    /** @test */
    public function test_20_payload_without_auth_token_is_rejected()
    {
        if (empty($this->authorityPrivateKey)) $this->markTestSkipped('Authority private key not found');
        $instId = InstallationService::getInstallationId();

        $licenseWithoutToken = [
            'license_id'      => 'LIC-NO-AUTH',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['*'],
        ];

        // Sign without auth_token
        $clean = $licenseWithoutToken;
        ksort($clean);
        $canonical = json_encode($clean, JSON_UNESCAPED_SLASHES);
        $binarySig = '';
        openssl_sign($canonical, $binarySig, $this->authorityPrivateKey, OPENSSL_ALGO_SHA256);
        $licenseWithoutToken['signature'] = base64_encode($binarySig);

        $v = LicenseVerifier::verify($licenseWithoutToken);
        $this->assertFalse($v['valid']);
    }

    /** @test */
    public function test_21_payload_with_fake_auth_token_is_rejected()
    {
        if (empty($this->authorityPrivateKey)) $this->markTestSkipped('Authority private key not found');
        $instId = InstallationService::getInstallationId();

        $licenseFakeToken = [
            'license_id'      => 'LIC-FAKE-AUTH',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+2 years')),
            'modules'         => ['*'],
            'auth_token'      => hash('sha256', 'wrong-passcode-guess:' . $instId),
        ];

        $clean = $licenseFakeToken;
        ksort($clean);
        $canonical = json_encode($clean, JSON_UNESCAPED_SLASHES);
        $binarySig = '';
        openssl_sign($canonical, $binarySig, $this->authorityPrivateKey, OPENSSL_ALGO_SHA256);
        $licenseFakeToken['signature'] = base64_encode($binarySig);

        $v = LicenseVerifier::verify($licenseFakeToken);
        $this->assertFalse($v['valid']);
    }

    /** @test */
    public function test_22_cli_command_validates_passcode()
    {
        // 1. Invalid passcode attempts must strictly fail
        $this->artisan('dapcode:sign-license', [
            '--passcode' => 'unauthorized-passcode-' . bin2hex(random_bytes(4)),
            '--key'      => $this->privKeyPath,
        ])->assertExitCode(1);

        // 2. Dynamic passcode verification using Authority environment variable if configured
        $authPasscode = env('DAPCODE_AUTHORITY_PASSCODE');
        if (!empty($authPasscode)) {
            $this->artisan('dapcode:sign-license', [
                '--passcode' => $authPasscode,
                '--key'      => $this->privKeyPath,
            ])->assertExitCode(0);
        }
    }
}
