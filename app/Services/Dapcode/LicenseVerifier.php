<?php

namespace App\Services\Dapcode;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LicenseVerifier
{
    /**
     * SHA-256 Digest of the Authority Master Secret Passcode (Encrypted in Code).
     */
    public const AUTH_HASH = 'b1976a157790447eb2cb85e6acc3df8b54e6fb39ea42b2b4184195d409b92233';

    /**
     * Default Public Verification Key (RSA-2048).
     * Used by the client to verify asymmetric digital signatures issued by the Authority.
     *
     * @var string
     */
    protected static $publicKeyPem = <<<EOT
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmAVWjY2K2u0pkH9unM5G
cBWvhVqWDTkSZDguSQvQshz/sIeR+CPdW/AoUSVBsLQp397EQUevkNjfnNa9Khyo
c0alj+klYdyqkoXmpSHJvUiQgac+QMQT3vpptOujnvaEDGmpozbBlvMKjaB/o3Pq
tZTMRkJd9EZjPCGDUBUlyJXZxiWOf4UmVWBdqVwSYkKaJo0+6o2JGSkGya6zkswm
rnqRIt36LVEhIloj48rhk8NJVk+l9qb5XIdDt2r3qIga/1TAhnlQjTGkJovpB63E
swBTm0YAGNrJKZM6aSWU02M/z3bfzkzGMwsh5Gsjhm2Z/2xKuMqVRhYbMmbRCkLF
swIDAQAB
-----END PUBLIC KEY-----
EOT;

    /**
     * Key ID rotation map.
     *
     * @var array<string, string>
     */
    protected static $trustedPublicKeys = [];

    /**
     * Verify the authenticity, integrity, expiration, and authorization of a signed license payload.
     *
     * @param array $license
     * @param string|null $moduleToCheck
     * @return array{valid: bool, reason: string}
     */
    public static function verify(array $license, ?string $moduleToCheck = null): array
    {
        // 1. Structure validation
        $requiredFields = ['license_id', 'installation_id', 'status', 'issued_at', 'expires_at', 'modules', 'auth_token', 'signature'];
        foreach ($requiredFields as $field) {
            if (!isset($license[$field])) {
                return ['valid' => false, 'reason' => "Missing license attribute: {$field}"];
            }
        }

        // 2. Encrypted Authority Token validation
        $expectedAuthToken = self::generateAuthToken((string) $license['license_id'], (string) $license['installation_id']);
        if (!hash_equals($expectedAuthToken, (string) $license['auth_token'])) {
            Log::warning('[AUDIT] EVENT: LICENSE_AUTH_TOKEN_INVALID', [
                'license_id'      => $license['license_id'],
                'installation_id' => $license['installation_id'],
            ]);
            return ['valid' => false, 'reason' => 'Encrypted Authority Token (auth_token) tidak valid atau tidak cocok.'];
        }

        // 3. Installation ID validation
        $currentInstallationId = InstallationService::getInstallationId();
        if ($license['installation_id'] !== '*' && $license['installation_id'] !== $currentInstallationId) {
            Log::warning('[AUDIT] EVENT: INSTALLATION_ID_MISMATCH', [
                'expected' => $currentInstallationId,
                'received' => $license['installation_id'],
            ]);
            return ['valid' => false, 'reason' => 'License is registered to a different installation.'];
        }

        // 4. Status validation
        if (strtoupper($license['status']) !== 'ACTIVE') {
            return ['valid' => false, 'reason' => "License status is {$license['status']}."];
        }

        // 5. Expiration validation
        if (!empty($license['expires_at'])) {
            $expiresAt = Carbon::parse($license['expires_at']);
            if (Carbon::now()->isAfter($expiresAt)) {
                Log::warning('[AUDIT] EVENT: LICENSE_EXPIRED', [
                    'license_id' => $license['license_id'],
                    'expired_at' => $license['expires_at'],
                ]);
                return ['valid' => false, 'reason' => "License expired on {$expiresAt->toFormattedDateString()}."];
            }
        }

        // 6. Asymmetric Cryptographic Digital Signature Verification
        $signature = $license['signature'];
        $keyId = $license['key_id'] ?? null;
        $payloadToVerify = self::canonicalizePayload($license);

        $isSignatureValid = self::verifyAsymmetricSignature($payloadToVerify, $signature, $keyId);
        if (!$isSignatureValid) {
            Log::warning('[AUDIT] EVENT: SIGNATURE_VERIFICATION_FAILED', [
                'license_id' => $license['license_id'],
            ]);
            return ['valid' => false, 'reason' => 'Cryptographic digital signature verification failed.'];
        }

        // 7. Module permission check
        if ($moduleToCheck !== null) {
            $allowedModules = (array) $license['modules'];
            $revokedModules = (array) ($license['revoked_modules'] ?? []);

            $normalizedCheck = strtolower($moduleToCheck);
            $normalizedAllowed = array_map('strtolower', $allowedModules);
            $normalizedRevoked = array_map('strtolower', $revokedModules);

            // Check if specific module has been revoked
            if (in_array($normalizedCheck, $normalizedRevoked, true)) {
                return ['valid' => false, 'reason' => "Modul [{$moduleToCheck}] telah dicabut dari lisensi ini."];
            }

            if (!in_array('*', $normalizedAllowed, true) && !in_array($normalizedCheck, $normalizedAllowed, true)) {
                return ['valid' => false, 'reason' => "Modul [{$moduleToCheck}] tidak termasuk dalam lisensi ini."];
            }
        }

        Log::info('[AUDIT] EVENT: LICENSE_VALIDATED', [
            'license_id'      => $license['license_id'],
            'installation_id' => $license['installation_id'],
            'module'          => $moduleToCheck ?? 'ALL',
        ]);

        return ['valid' => true, 'reason' => 'License is valid and active.'];
    }

    /**
     * Validate the secret passcode entered during license signing using constant-time cryptographic hash comparison.
     * No plaintext passcodes are stored in the source code.
     *
     * @param string $passcode
     * @return bool
     */
    public static function verifyPasscode(string $passcode): bool
    {
        if (empty($passcode)) {
            return false;
        }

        $inputHash = hash('sha256', (string) $passcode);

        $envPass = env('DAPCODE_AUTHORITY_PASSCODE');
        if (!empty($envPass)) {
            $envHash = hash('sha256', (string) $envPass);
            if (hash_equals($envHash, $inputHash)) {
                return true;
            }
        }

        $envPassHash = env('DAPCODE_AUTHORITY_PASSCODE_HASH');
        if (!empty($envPassHash) && hash_equals((string) $envPassHash, $inputHash)) {
            return true;
        }

        return hash_equals(self::AUTH_HASH, $inputHash)
            || hash_equals('b1976a157790447eb2cb85e6acc3df8b54e6fb39ea42b2b4184195d409b92233', $inputHash);
    }

    /**
     * Generate an encrypted authority token for the payload based on hashed secret passcode.
     *
     * @param string $licenseId
     * @param string $installationId
     * @param string $action
     * @return string
     */
    public static function generateAuthToken(string $licenseId, string $installationId, string $action = 'ACTIVATE'): string
    {
        $suffix = in_array(strtoupper($action), ['REVOKE', 'REVOKE_MODULES'], true) ? ':REVOKE' : '';
        return hash('sha256', self::AUTH_HASH . ':' . $licenseId . ':' . $installationId . $suffix);
    }

    /**
     * Canonicalize payload for consistent cryptographic hashing and verification.
     * Field 'signature' and runtime 'activated_at' are strictly excluded from signing payload.
     *
     * @param array $license
     * @return string
     */
    public static function canonicalizePayload(array $license): string
    {
        $clean = $license;
        unset($clean['signature'], $clean['activated_at'], $clean['revoked_at'], $clean['revocation_reason'], $clean['revoked_modules']);
        ksort($clean);

        if (isset($clean['modules']) && is_array($clean['modules'])) {
            sort($clean['modules']);
        }

        return json_encode($clean, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Verify asymmetric digital signature using RSA-2048 and SHA-256 digest algorithm.
     *
     * @param string $canonicalPayload
     * @param string $base64Signature
     * @param string|null $keyId
     * @return bool
     */
    public static function verifyAsymmetricSignature(string $canonicalPayload, string $base64Signature, ?string $keyId = null): bool
    {
        $publicKey = self::getPublicKey($keyId);
        if (empty($publicKey)) {
            return false;
        }

        $binarySignature = base64_decode($base64Signature, true);
        if ($binarySignature === false) {
            return false;
        }

        $result = openssl_verify(
            $canonicalPayload,
            $binarySignature,
            $publicKey,
            OPENSSL_ALGO_SHA256
        );

        return $result === 1;
    }

    /**
     * Retrieve the public verification key.
     *
     * @param string|null $keyId
     * @return string|resource
     */
    public static function getPublicKey(?string $keyId = null)
    {
        if ($keyId && isset(static::$trustedPublicKeys[$keyId])) {
            return static::$trustedPublicKeys[$keyId];
        }

        $keyFile = config('dapcode.files.public_key', storage_path('app/dapcode/public_key.pem'));
        if (file_exists($keyFile)) {
            return file_get_contents($keyFile);
        }

        return static::$publicKeyPem;
    }
}
