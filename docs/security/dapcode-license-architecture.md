# 🏛️ DapCode License — Architecture, Verification & Hardening Guide

## 1. Cryptographic Architecture Overview

Sistem lisensi DapCode menggunakan model kriptografi kunci asimetris (*Asymmetric Public-Key Cryptography*) standar industri:

```text
               DAPCODE LICENSE AUTHORITY (OWNER)
                             │
                      RSA PRIVATE KEY
                             │
                             ▼
                    Sign License / Revoke
                             │
                      Base64 Signature
                             │
                             ▼
                 CLIENT APPLICATION (LARAVEL)
                             │
                      RSA PUBLIC KEY
                             │
                             ▼
                      LicenseVerifier
```

- **Algoritma:** RSA-SHA256 (2048-bit modulus).
- **Public Key:** Disertakan dalam [LicenseVerifier.php](file:///c:/laragon/www/dapcode-app/app/Services/Dapcode/LicenseVerifier.php) (atau dimuat via `storage/app/dapcode/public_key.pem`).
- **Private Key:** Disimpan murni dan terisolasi pada DapCode License Authority Server milik Owner.

---

## 2. Deterministic Canonicalization

Untuk mencegah variasi representasi JSON yang dapat merusak validasi tanda tangan (*signature verification*), data dinormalisasi secara deterministik sebelum proses signing maupun verification:

```php
public static function canonicalizePayload(array $license): string
{
    $clean = $license;
    // Hapus atribut runtime yang berubah-ubah
    unset($clean['signature'], $clean['activated_at'], $clean['revoked_at'], $clean['revocation_reason']);
    
    // Urutkan key alfabetis
    ksort($clean);

    // Urutkan array modul
    if (isset($clean['modules']) && is_array($clean['modules'])) {
        sort($clean['modules']);
    }

    return json_encode($clean, JSON_UNESCAPED_SLASHES);
}
```

---

## 3. Evaluation of Verification Modes

### 3.1. Mode A: Offline License Verification (Current Default)
- **Mekanisme:** Lisensi ditandatangani sekali oleh Authority, diaktivasi oleh klien, dan diverifikasi secara lokal menggunakan Public Key.
- **Kelebihan:**
  - 100% Berfungsi tanpa koneksi internet (*Air-gapped / Isolated environments*).
  - Waktu eksekusi ultra-cepat ($< 1\text{ ms}$).
  - Privasi data klien terjaga penuh.
- **Keterbatasan:**
  - Pencabutan lisensi (*Revocation*) membutuhkan *Signed Revocation Token* yang dimasukkan ke aplikasi klien.
  - Bergantung pada jam sistem lokal (*System Clock*) untuk validasi tanggal kedaluwarsa.

---

### 3.2. Mode B: Online Verification & Heartbeat (Recommended Extension)
- **Mekanisme:** Aplikasi klien secara periodik (misal setiap 12–24 jam) melakukan sinkronisasi status ke Authority API:
  `POST https://license.dapcode.com/api/v1/verify`
- **Arsitektur Caching & Offline Grace Period:**

```text
HTTP Request
     │
     ▼
Local License Cache Check
     │
     ├── Cache Masih Valid (TTL misal 12 Jam) ───► ALLOW (Local Verify)
     │
     └── Cache Expired (Melewati TTL)
              │
              ▼
         Authority API Call
              │
              ├── [Online OK] Status ACTIVE ─────► Update Cache TTL & ALLOW
              ├── [Online OK] Status REVOKED ────► Revoke License & DENY (403)
              │
              └── [Network Error / Timeout]
                       │
                       ├── Dalam Offline Grace Period (misal 14 Hari) ──► ALLOW (Fallback Offline)
                       └── Melewati Offline Grace Period ──────────────► DENY (403 - Verification Required)
```

- **Manfaat:**
  - Revokasi lisensi dapat merambat secara otomatis (*Instant Remote Revocation*).
  - Mengeliminasi risiko manipulasi jam sistem klien karena timestamp divalidasi oleh server Authority.

---

## 4. Key Management & Key Rotation Strategy

Jika di masa mendatang Authority perlu memperbarui kunci kriptografis (*Key Rotation*):

### 4.1. Desain Multi-Key Verification (`key_id`)
Tambahkan atribut `key_id` pada header payload lisensi:
```json
{
    "key_id": "auth-2026-v1",
    "license_id": "LIC-2026-PRO-001",
    "installation_id": "DAP-...",
    ...
}
```
Client dapat memelihara array Public Keys terpercaya:
```php
protected const TRUSTED_PUBLIC_KEYS = [
    'auth-2026-v1' => "-----BEGIN PUBLIC KEY-----\n...",
    'auth-2027-v1' => "-----BEGIN PUBLIC KEY-----\n...",
];
```
Authority dapat menerbitkan lisensi baru dengan key baru tanpa membatalkan lisensi lama yang belum kedaluwarsa.

---

## 5. Storage & Logging Hardening

1. **Private Storage:** Direktori `storage/app/dapcode/` dilindungi permission `0600` dan diabaikan oleh `.gitignore`.
2. **Sanitized Audit Logs:** Log pada `storage/logs/laravel.log` mencatat event (`ACTIVATION_SUCCESS`, `LICENSE_VALIDATED`, `LICENSE_REVOKED`, dll.) dengan konteks aman tanpa pernah mencetak private key, token rahasia, atau raw binary credential.
3. **Fail-Closed Principle:** Segala bentuk anomali (file hilang, JSON rusak, signature tidak cocok, tanggal kedaluwarsa, module mismatch) secara default memicu penguncian modul terlindungi (**HTTP 403 Forbidden**) dengan aman tanpa mengganggu halaman publik atau merusak data aplikasi.
