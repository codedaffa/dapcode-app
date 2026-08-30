<?php

namespace App\Http\Controllers\Dapcode;

use App\Http\Controllers\Controller;
use App\Services\Dapcode\ActivationService;
use App\Services\Dapcode\LicenseGuard;
use App\Services\Dapcode\LicenseVerifier;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    /**
     * Show Activation View.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function showActivate(Request $request)
    {
        return $this->render('dapcode.activate', [
            'title'          => 'Aktivasi Lisensi DapCode',
            'pageTitle'      => 'Aktivasi Lisensi Modul DapCode',
            'installationId' => LicenseGuard::getInstallationId(),
            'licenseStatus'  => LicenseGuard::getStatus(),
            'license'        => LicenseGuard::getLicense(),
        ]);
    }

    /**
     * Process License Activation.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function activate(Request $request)
    {
        $payload = $request->input('license_payload');

        if (empty($payload)) {
            if ($request->expectsJson()) {
                return $this->jsonError('Payload lisensi tidak boleh kosong.', 422);
            }
            return redirect()->back()->with('error', 'Payload lisensi tidak boleh kosong.');
        }

        LicenseGuard::clearCache();
        $result = ActivationService::activate($payload);

        if (!$result['success']) {
            if ($request->expectsJson()) {
                return $this->jsonError($result['message'], 400);
            }
            return redirect()->back()->with('error', $result['message']);
        }

        if ($request->expectsJson()) {
            return $this->jsonResponse($result['data'] ?? [], $result['message']);
        }

        return redirect()->route('dapcode.activate')->with('success', $result['message']);
    }

    /**
     * Process License Deactivation / Revocation.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function deactivate(Request $request)
    {
        $payload = $request->input('revocation_payload') ?? $request->input('authorization_token');
        $reason = $request->input('reason', 'Manual Revocation via Authority');

        if (empty($payload)) {
            if ($request->expectsJson()) {
                return $this->jsonError('Signed Revocation Token diperlukan untuk deaktivasi.', 422);
            }
            return redirect()->back()->with('error', 'Signed Revocation Token diperlukan untuk deaktivasi.');
        }

        LicenseGuard::clearCache();
        $result = ActivationService::deactivate($payload, $reason);

        if (!$result['success']) {
            if ($request->expectsJson()) {
                return $this->jsonError($result['message'], 403);
            }
            return redirect()->back()->with('error', $result['message']);
        }

        if ($request->expectsJson()) {
            return $this->jsonResponse([], $result['message']);
        }

        return redirect()->route('dapcode.activate')->with('success', $result['message']);
    }

    /**
     * Return JSON status of the installation and license.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        return response()->json([
            'status'          => LicenseGuard::getStatus(),
            'installation_id' => LicenseGuard::getInstallationId(),
            'is_activated'    => LicenseGuard::isActivated(),
            'modules'         => LicenseGuard::getAllowedModules(),
            'license'         => LicenseGuard::getLicense(),
        ]);
    }

    /**
     * Show Authority Web Terminal View.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function showTerminal(Request $request)
    {
        $allModules = [
            'dashboard'     => 'Dashboard & Overview',
            'profile'       => 'Biodata & Identitas',
            'education'     => 'Riwayat Pendidikan',
            'certification' => 'Sertifikasi Profesi',
            'achievement'   => 'Prestasi & Award',
            'interest'      => 'Bidang Minat & Keahlian',
            'project'       => 'Portofolio Project',
            'research'      => 'Riset & Publikasi Ilmiah',
            'career'        => 'Pengalaman Karir & Kerja',
            'activity'      => 'Organisasi & Kegiatan',
            'media'         => 'Galeri Multimedia',
            'commerce'      => 'Katalog Produk & Commerce',
            'setting'       => 'Pengaturan Sistem',
        ];

        return $this->render('dapcode.authority-terminal', [
            'title'            => 'Authority Terminal — DapCode Signer Server',
            'pageTitle'        => 'DapCode License Authority Terminal',
            'currentInstId'    => LicenseGuard::getInstallationId(),
            'currentLicenseId' => LicenseGuard::getLicense()['license_id'] ?? '',
            'modules'          => $allModules,
        ]);
    }

    /**
     * Process Cryptographic Signing via Authority Web Terminal.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signPayload(Request $request)
    {
        $passcode = $request->input('passcode');
        if (empty($passcode) || !LicenseVerifier::verifyPasscode($passcode)) {
            return response()->json([
                'success' => false,
                'message' => '[SECURITY ACCESS DENIED] Passcode Authority tidak valid!',
                'log'     => "AUTH_GATE: Validation failed for client request.\nACCESS_DENIED: Invalid Master Security Passcode.",
            ], 403);
        }

        $keyPath = env('DAPCODE_AUTHORITY_KEY_PATH') 
            ?: 'C:\Users\po\.gemini\antigravity-ide\brain\990a2152-70dc-4fd4-a1f5-79df37e16c3c\authority_private_key.pem';

        if (!file_exists($keyPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Authority Private Key tidak ditemukan di server.',
                'log'     => "KEY_RESOLVE_ERROR: Private signing key file not found at: {$keyPath}",
            ], 500);
        }

        $privateKeyContent = file_get_contents($keyPath);
        $targetInstallationId = $request->input('installation_id') ?: LicenseGuard::getInstallationId();
        $action = $request->input('action', 'ACTIVATE');

        if ($action === 'REVOKE') {
            $licenseId = $request->input('license_id', 'LIC-' . date('Y') . '-REV');
            $reason = $request->input('reason', 'Pencabutan Resmi via Authority Web Terminal');
            $revokedModules = $request->input('modules', ['*']);

            $authToken = LicenseVerifier::generateAuthToken($licenseId, $targetInstallationId, 'REVOKE');

            $payload = [
                'action'          => 'REVOKE',
                'license_id'      => $licenseId,
                'installation_id' => $targetInstallationId,
                'revoked_at'      => date('c'),
                'reason'          => $reason,
                'auth_token'      => $authToken,
            ];

            if (!empty($revokedModules) && !in_array('*', $revokedModules, true)) {
                $payload['revoked_modules'] = $revokedModules;
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

            return response()->json([
                'success' => true,
                'type'    => 'SIGNED_REVOCATION_TOKEN',
                'payload' => $payload,
                'log'     => "AUTHORITY_SIGNER: Generating Signed Revocation Token...\nCANONICAL_HASH: SHA-256 Digest Computed.\nRSA_SIGN: 2048-bit Private Key Signature Applied.\nSTATUS: TOKEN_READY",
            ]);
        }

        // Action == ACTIVATE
        $selectedModules = $request->input('modules', ['*']);
        $years = (int) $request->input('years', 2);
        if ($years < 1) $years = 2;

        $licenseId = 'LIC-' . date('Y') . '-PRO-' . strtoupper(bin2hex(random_bytes(3)));
        $issuedAt = date('c');
        $expiresAt = date('c', strtotime("+{$years} years"));

        $authToken = LicenseVerifier::generateAuthToken($licenseId, $targetInstallationId, 'ACTIVATE');

        $payload = [
            'license_id'      => $licenseId,
            'installation_id' => $targetInstallationId,
            'status'          => 'ACTIVE',
            'issued_at'       => $issuedAt,
            'expires_at'      => $expiresAt,
            'modules'         => $selectedModules,
            'auth_token'      => $authToken,
        ];

        $canonical = LicenseVerifier::canonicalizePayload($payload);
        $binarySig = '';
        openssl_sign($canonical, $binarySig, $privateKeyContent, OPENSSL_ALGO_SHA256);
        $payload['signature'] = base64_encode($binarySig);

        return response()->json([
            'success' => true,
            'type'    => 'SIGNED_ACTIVATION_LICENSE',
            'payload' => $payload,
            'log'     => "AUTHORITY_SIGNER: Initializing RSA-2048 Private Signing Engine...\nPASSCODE_AUTH: Verified.\nAUTH_TOKEN: Hash Token Derived.\nCANONICALIZE: Deterministic JSON Encoding Complete.\nASYMMETRIC_SIGN: 2048-bit Signature Generated.\nSTATUS: LICENSE_PAYLOAD_SUCCESS",
        ]);
    }
}
