<?php

namespace App\Http\Controllers\Dapcode;

use App\Http\Controllers\Controller;
use App\Services\Dapcode\ActivationService;
use App\Services\Dapcode\LicenseGuard;
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
        $status = LicenseGuard::getStatus();
        $license = LicenseGuard::getLicense();

        return response()->json([
            'installation_id' => LicenseGuard::getInstallationId(),
            'status'          => $status,
            'is_activated'    => LicenseGuard::isActivated(),
            'license_id'      => $license['license_id'] ?? null,
            'modules'         => $license['modules'] ?? [],
            'expires_at'      => $license['expires_at'] ?? null,
        ]);
    }
}
