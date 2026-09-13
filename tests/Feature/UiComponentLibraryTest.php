<?php

namespace Tests\Feature;

use App\Services\Dapcode\ActivationService;
use App\Services\Dapcode\InstallationService;
use App\Services\Dapcode\IntegrityService;
use App\Services\Dapcode\LicenseGuard;
use App\Services\Dapcode\LicenseVerifier;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class UiComponentLibraryTest extends TestCase
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
        $this->activateTestLicense();
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

    protected function activateTestLicense(): void
    {
        if (empty($this->authorityPrivateKey)) return;

        $this->resetLicenseFiles();
        IntegrityService::recordCoreFilesManifest();

        $instId = InstallationService::getInstallationId();
        $payload = [
            'license_id'      => 'LIC-TEST-UI-001',
            'installation_id' => $instId,
            'status'          => 'ACTIVE',
            'issued_at'       => date('c'),
            'expires_at'      => date('c', strtotime('+1 year')),
            'modules'         => ['*'],
            'auth_token'      => LicenseVerifier::generateAuthToken('LIC-TEST-UI-001', $instId, 'ACTIVATE'),
        ];

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

        ActivationService::activate($payload);
        LicenseGuard::clearCache();
    }

    /**
     * Test UI Showcase page is accessible and returns HTTP 200.
     */
    public function test_ui_showcase_page_loads_successfully()
    {
        $response = $this->get('/dapcode/ui-showcase');
        $response->assertStatus(200);
        $response->assertSee('UI Component Showcase');
        $response->assertSee('ui-btn', false);
        $response->assertSee('ui-card', false);
        $response->assertSee('ui-badge', false);
        $response->assertSee('Lihat Kode');
        $response->assertSee('ui-code-drawer', false);
        $response->assertSee('Panduan &amp; Integrasi', false);
        $response->assertSee('Salin Kode');
    }

    /**
     * Test button component rendering.
     */
    public function test_ui_button_component_rendering()
    {
        $rendered = Blade::render('<x-ui.button variant="danger" size="lg" icon="fa-solid fa-trash">Hapus</x-ui.button>');
        $this->assertStringContainsString('ui-btn', $rendered);
        $this->assertStringContainsString('ui-btn-danger', $rendered);
        $this->assertStringContainsString('ui-btn-lg', $rendered);
        $this->assertStringContainsString('fa-solid fa-trash', $rendered);
        $this->assertStringContainsString('Hapus', $rendered);

        // Test link variant
        $renderedLink = Blade::render('<x-ui.button href="/test-url" variant="primary">Tautan</x-ui.button>');
        $this->assertStringContainsString('<a href="/test-url"', $renderedLink);
        $this->assertStringContainsString('ui-btn-primary', $renderedLink);
    }

    /**
     * Test badge component rendering.
     */
    public function test_ui_badge_component_rendering()
    {
        $rendered = Blade::render('<x-ui.badge variant="success" style="solid" pill dot>Aktif</x-ui.badge>');
        $this->assertStringContainsString('ui-badge', $rendered);
        $this->assertStringContainsString('ui-badge-solid', $rendered);
        $this->assertStringContainsString('ui-badge-success', $rendered);
        $this->assertStringContainsString('ui-badge-pill', $rendered);
        $this->assertStringContainsString('ui-badge-dot', $rendered);
        $this->assertStringContainsString('Aktif', $rendered);
    }

    /**
     * Test alert component rendering.
     */
    public function test_ui_alert_component_rendering()
    {
        $rendered = Blade::render('<x-ui.alert variant="warning" title="Peringatan Penting" dismissible accent>Harap simpan data Anda.</x-ui.alert>');
        $this->assertStringContainsString('ui-alert', $rendered);
        $this->assertStringContainsString('ui-alert-warning', $rendered);
        $this->assertStringContainsString('ui-alert-accent', $rendered);
        $this->assertStringContainsString('Peringatan Penting', $rendered);
        $this->assertStringContainsString('Harap simpan data Anda.', $rendered);
        $this->assertStringContainsString('ui-alert-close', $rendered);
    }

    /**
     * Test card component rendering.
     */
    public function test_ui_card_component_rendering()
    {
        $rendered = Blade::render('
            <x-ui.card title="Judul Kartu" subtitle="Sub Judul" icon="fa-solid fa-gear">
                <p>Isi Konten Kartu</p>
                <x-slot name="actions">
                    <x-ui.button size="sm">Aksi</x-ui.button>
                </x-slot>
            </x-ui.card>
        ');
        $this->assertStringContainsString('ui-card', $rendered);
        $this->assertStringContainsString('Judul Kartu', $rendered);
        $this->assertStringContainsString('Sub Judul', $rendered);
        $this->assertStringContainsString('Isi Konten Kartu', $rendered);
        $this->assertStringContainsString('ui-card-actions', $rendered);
    }

    /**
     * Test form controls (input, select, textarea, checkbox).
     */
    public function test_ui_form_controls_rendering()
    {
        $renderedInput = Blade::render('<x-ui.input name="username" label="Nama Pengguna" icon="fa-regular fa-user" required />');
        $this->assertStringContainsString('Nama Pengguna', $renderedInput);
        $this->assertStringContainsString('ui-input', $renderedInput);
        $this->assertStringContainsString('name="username"', $renderedInput);
        $this->assertStringContainsString('required', $renderedInput);

        $renderedSelect = Blade::render('<x-ui.select name="role" label="Role" :options="[\'admin\' => \'Admin\', \'user\' => \'User\']" selected="admin" />');
        $this->assertStringContainsString('ui-select', $renderedSelect);
        $this->assertStringContainsString('value="admin"', $renderedSelect);
        $this->assertStringContainsString('selected', $renderedSelect);

        $renderedSwitch = Blade::render('<x-ui.checkbox name="active" label="Aktifkan Fitur" switch checked />');
        $this->assertStringContainsString('ui-switch', $renderedSwitch);
        $this->assertStringContainsString('checked', $renderedSwitch);
    }

    /**
     * Test modal and tabs components rendering.
     */
    public function test_ui_modal_and_tabs_rendering()
    {
        $renderedModal = Blade::render('<x-ui.modal id="modal-test" title="Judul Modal"><p>Isi Modal</p></x-ui.modal>');
        $this->assertStringContainsString('id="modal-test"', $renderedModal);
        $this->assertStringContainsString('ui-modal-backdrop', $renderedModal);
        $this->assertStringContainsString('Judul Modal', $renderedModal);

        $renderedTabs = Blade::render('<x-ui.tabs :items="[[\'id\' => \'t1\', \'label\' => \'Tab 1\'], [\'id\' => \'t2\', \'label\' => \'Tab 2\']]" active="t1">Konten Tab</x-ui.tabs>');
        $this->assertStringContainsString('data-ui-tab-target="t1"', $renderedTabs);
        $this->assertStringContainsString('Tab 1', $renderedTabs);
    }

    /**
     * Test datatable component with auto-refresh and refreshable props.
     */
    public function test_ui_datatable_auto_refresh_rendering()
    {
        $rendered = Blade::render('<x-ui.datatable id="test-dt" endpoint="/api/test" refreshable auto-refresh="5000" auto-refresh-toggle><p>Data</p></x-ui.datatable>');
        $this->assertStringContainsString('id="test-dt"', $rendered);
        $this->assertStringContainsString('data-ui-refresh-url="/api/test"', $rendered);
        $this->assertStringContainsString('data-ui-auto-refresh="5000"', $rendered);
        $this->assertStringContainsString('data-ui-table-refresh="test-dt"', $rendered);
        $this->assertStringContainsString('data-ui-table-auto-refresh="test-dt"', $rendered);
        $this->assertStringContainsString('Auto Refresh', $rendered);
    }

    /**
     * Test refactored Setting module index page.
     */
    public function test_setting_module_renders_with_ui_components()
    {
        $response = $this->get('/setting');
        $response->assertStatus(200);
        $response->assertSee('ui-card', false);
        $response->assertSee('ui-btn', false);
        $response->assertSee('section-language', false);
        $response->assertSee('section-themes', false);
    }

    /**
     * Test Vite compiled assets exist.
     */
    public function test_vite_asset_manifest_exists()
    {
        $manifestPath = public_path('build/manifest.json');
        $this->assertTrue(File::exists($manifestPath), 'Vite build manifest must exist');
        
        $manifest = json_decode(File::get($manifestPath), true);
        $this->assertArrayHasKey('resources/css/app.css', $manifest);
        $this->assertArrayHasKey('resources/js/app.js', $manifest);
    }

    /**
     * Test portfolio homepage integration with UI Library & Vite assets.
     */
    public function test_portfolio_page_integrates_ui_components()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('/dapcode/ui-showcase');
        $response->assertSee('UI Component Library');
        $response->assertSee('ui-toast-container', false);
    }

    /**
     * Test activate and terminal pages integrate with layout and UI Library.
     */
    public function test_activate_and_terminal_pages_integrate_ui_components()
    {
        $activateResp = $this->get('/dapcode/activate');
        $activateResp->assertStatus(200);
        $activateResp->assertSee('Aktivasi Lisensi DapCode');
        $activateResp->assertSee('Unique Installation ID');

        $terminalResp = $this->get('/dapcode/terminal');
        $terminalResp->assertStatus(200);
        $terminalResp->assertSee('DapCode Developer Web Terminal');
    }
}



