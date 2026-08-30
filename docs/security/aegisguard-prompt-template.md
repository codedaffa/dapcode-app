# 🛡️ DAPCODE AEGISGUARD™ — MASTER IMPLEMENTATION PROMPT TEMPLATE

> **Panduan Penggunaan:** Salin seluruh isi template prompt di bawah ini dan tempelkan (*copy-paste*) ke AI Coding Assistant (Antigravity, Claude, ChatGPT, Cursor, dll.) pada proyek Laravel baru Anda untuk mengimplementasikan **DapCode AegisGuard™** secara otomatis dan terstandarisasi.

---

```markdown
# 🛡️ SYSTEM PROMPT: IMPLEMENTASI DAPCODE AEGISGUARD™ SECURITY ENGINE

Anda bertindak sebagai **Principal Security Architect & Senior Laravel Engineer**.

Tugas Anda adalah mengimplementasikan **DapCode AegisGuard™ (Asymmetric Cryptographic RSA-2048 & Fail-Closed Module Activation Engine)** secara lengkap, tangguh, dan siap produksi ke dalam repositori aplikasi Laravel ini.

---

## 🏛️ 1. ARSITEKTUR & PRINSIP UTAMA AEGISGUARD™

1. **Model Kriptografi Asimetris Murni (Asymmetric Trust Model):**
   - **Client Side (Aplikasi Klien):** HANYA menyimpan *Public Verification Key* (`storage/app/dapcode/public_key.pem` / hardcoded fallback di `LicenseVerifier.php`) dan konstanta hash otorisasi `AUTH_HASH` (SHA-256 dari Passcode Otoritas). Klien **TIDAK PERNAH** memiliki Private Key.
   - **Authority Side (Pemilik / Server Otoritas):** Memegang *Private Signing Key* (`authority_private_key.pem`) dan *Master Secret Passcode* untuk menandatangani payload lisensi dan token pencabutan (*revocation token*).
2. **Zero Secrets in Client Repository:**
   - Tidak boleh ada private key, password plaintext, master secret, atau backdoor bypass di dalam repositori klien atau `.env`.
3. **Global Fail-Closed Enforcement:**
   - Seluruh rute aplikasi (web & api) **wajib terproteksi secara global**. Jika belum diaktivasi, lisensi kadaluwarsa, dirusak (*tampered*), atau dicabut (*revoked*), aplikasi wajib merespons **HTTP 403 Forbidden** (kecuali rute aktivasi `/dapcode/*` dan asset statis).
4. **Dynamic Module Auto-Discovery:**
   - Modul yang ada di dalam `app/Modules/` atau terdaftar di konfigurasi wajib otomatis terdeteksi dan terproteksi per modul (*granular access control*).
5. **Anti-Tampering & Granular Signed Revocation:**
   - Setiap modifikasi ilegal pada file lisensi lokal akan langsung memicu penguncian otomatis.
   - Pencabutan lisensi (penuh maupun per-modul) wajib diverifikasi menggunakan *Signed Revocation Token* bertanda tangan RSA-2048 yang sah.

---

## 📁 2. KOMPONEN FILE YANG HARUS DIBUAT / DIPERBARUI

### A. Konfigurasi (`config/dapcode.php`)
Buat file konfigurasi yang memuat:
- Path file runtime lisensi: `.installation`, `.license`, `.license-state`, `public_key.pem` (tersimpan privat di `storage/app/dapcode/`).
- Array `modules` & `protected_modules`.
- Array `excluded_routes` (misal: `'dapcode/*', 'dapcode', 'build/*', 'assets/*', 'favicon.ico'`).

### B. Core Services (`app/Services/Dapcode/`)

1. **`InstallationService.php`:**
   - Menghasilkan dan memvalidasi `Installation ID` persisten unik per mesin (format: `DAP-[HASH]-[UUID]`).
   - Disimpan di `storage/app/dapcode/.installation` dengan proteksi permission.

2. **`LicenseVerifier.php`:**
   - Konstanta `AUTH_HASH = hash('sha256', '<MASTER_PASSCODE>');`
   - `verifyPasscode($passcode)`: Validasi passcode menggunakan `hash_equals()`.
   - `generateAuthToken($licenseId, $installationId, $action = 'ACTIVATE')`: Menghasilkan token otorisasi kriptografis SHA-256.
   - `canonicalizePayload(array $license)`: Menghasilkan string JSON kanonikal deterministik (mengabaikan field `signature`, `activated_at`, `revoked_at`, `revocation_reason`, `revoked_modules` dan mengurutkan keys secara alfabetis).
   - `verifyAsymmetricSignature($canonicalPayload, $base64Signature, $keyId = null)`: Memvalidasi tanda tangan digital RSA-2048 dengan SHA-256 menggunakan `openssl_verify()`.
   - `verify($license, $moduleToCheck = null)`: Verifikasi menyeluruh (kelengkapan atribut, validasi `auth_token`, `installation_id`, `status == ACTIVE`, `expires_at`, `openssl_verify`, dan granular per-modul).

3. **`IntegrityService.php`:**
   - Mengelola checksum integritas SHA-256 lokal (`.license-state`) untuk mendeteksi modifikasi langsung pada file lisensi.

4. **`LicenseGuard.php`:**
   - Central evaluator dengan in-memory request caching:
     - `canAccessApplication()`: Mengecek integritas, status ACTIVE, masa berlaku, dan validasi signature lisensi global.
     - `isModuleAllowed(string $moduleName)`: Mengecek apakah modul tertentu diizinkan dan belum dicabut.
     - `getAllAvailableModules()`: Memindai dinamis direktori `app/Modules/` dan menggabungkannya dengan konfigurasi.
     - `getAllowedModules()`: Mengembalikan daftar modul aktif saat ini.
     - `getStatus()`: Mengembalikan `ACTIVE`, `PENDING`, `EXPIRED`, `REVOKED`, `CORRUPTED`, atau `INVALID`.

5. **`ActivationService.php`:**
   - `activate($licensePayload)`: Memverifikasi tanda tangan digital RSA-2048 payload lisensi baru, mencocokkan Installation ID, menyimpan ke `.license`, mengupdate integrity checksum, dan me-rotate secret.
   - `deactivate($revocationInput, $reason)`: Memverifikasi *Signed Revocation Token*, memproses pencabutan penuh (`REVOKE`) atau pencabutan modul parsial (`revoked_modules`), dan memperbarui file lisensi lokal.

---

### C. Global Middleware (`app/Http/Middleware/DapcodeLicenseMiddleware.php` & `app/Http/Kernel.php`)

1. **`DapcodeLicenseMiddleware.php`:**
   - Mengecualikan `excluded_routes`.
   - Memvalidasi `LicenseGuard::canAccessApplication()`. Jika gagal, kembalikan response **403 Forbidden** (JSON untuk API / View `dapcode.license-required` untuk Web).
   - Memvalidasi `LicenseGuard::isModuleAllowed($targetModule)` jika rute menargetkan modul spesifik.
2. **`app/Http/Kernel.php`:**
   - Daftarkan `\App\Http\Middleware\DapcodeLicenseMiddleware::class` di dalam grup `$middlewareGroups['web']` dan `$middlewareGroups['api']`.

---

### D. Authority Signer Tools

1. **Artisan CLI Command (`app/Console/Commands/SignDapcodeLicense.php`):**
   - Command: `php artisan dapcode:sign-license {installation_id?}`
   - Opsi: `--modules=*`, `--years=2`, `--revoke`, `--license_id=`, `--passcode=`.
   - Meminta input passcode rahasia (disembunyikan di terminal) jika `--passcode` tidak disertakan.
   - Memvalidasi passcode via `LicenseVerifier::verifyPasscode()`.
   - Mengambil private key lokal/environment, membuat `auth_token`, melakukan canonicalization, menandatangani dengan `openssl_sign()`, dan mencetak output JSON yang siap pakai.

2. **Web Controller & Routes (`app/Http/Controllers/Dapcode/LicenseController.php` & `routes/web.php`):**
   - `GET  /dapcode/activate`: Tampilan antarmuka aktivasi & tombol salin Installation ID.
   - `POST /dapcode/activate`: Endpoint aktivasi lisensi.
   - `POST /dapcode/deactivate`: Endpoint pencabutan lisensi.
   - `GET  /dapcode/status`: Endpoint status JSON.
   - `GET  /dapcode/terminal`: Konsol Authority Web Terminal & HSM Signer UI.
   - `POST /dapcode/terminal/sign`: Endpoint penandatanganan payload kriptografis.

3. **Views Blade:**
   - `resources/views/dapcode/activate.blade.php`: Form aktivasi dan deaktivasi visual.
   - `resources/views/dapcode/license-required.blade.php`: Halaman 403 Forbidden yang elegan saat lisensi terkunci.
   - `resources/views/dapcode/authority-terminal.blade.php`: Konsol interaktif dark-cyberpunk untuk menandatangani payload dengan 1-klik.

---

### E. Automated Security Test Suite (`tests/Feature/DapcodeLicenseSecurityTest.php`)

Wajib membuat 22 automated test cases komprehensif menggunakan PHPUnit:
1. Fresh clone tanpa lisensi mengembalikan 403 pada rute publik dan modul.
2. Halaman aktivasi `/dapcode/activate` tetap bisa diakses (200 OK).
3. Aktivasi dengan lisensi bertanda-tangan sah berhasil membuka akses (200 OK).
4. Pemalsuan data lisensi (*tampering*) langsung ditolak oleh signature verification.
5. Lisensi dengan Installation ID mesin lain ditolak (*machine lock*).
6. Lisensi kedaluwarsa (*expired*) otomatis mengembalikan status EXPIRED & 403.
7. Lisensi parsial hanya membuka modul yang diizinkan, modul lain tetap 403.
8. Modul baru yang ditambahkan otomatis terproteksi.
9. Pencabutan lisensi penuh (*Signed Full Revocation*) menonaktifkan aplikasi (403).
10. Pencabutan parsial (*Signed Granular Revocation*) hanya mengunci modul yang dicabut.
11. Passcode yang salah pada Signer Console langsung diblokir (403 Access Denied).
12. Menghapus atau me-rename modul tidak merusak integritas sistem lisensi.

---

## 🔒 3. ATURAN KEAMANAN IMPLEMENTASI

- **JANGAN PERNAH** membuat backdoor seperti `if (app()->environment('local')) return true;` atau `env('SKIP_LICENSE')`.
- **JANGAN PERNAH** mengekspos plaintext passcode atau private key di dalam file kode aplikasi atau repositori publik.
- **SELALU** pastikan file lisensi runtime (`storage/app/dapcode/`) masuk ke `.gitignore`.
- Jalankan `php vendor/phpunit/phpunit/phpunit tests/Feature/DapcodeLicenseSecurityTest.php` untuk memastikan seluruh 22 pengujian keamanan lolos 100% (*Pass*).
```

---

## 📋 DAFTAR FILE HASIL IMPLEMENTASI
Setelah prompt di atas dijalankan oleh AI Agent, proyek baru Anda akan memiliki arsitektur keamanan lengkap berikut:

| File | Peran / Deskripsi |
|---|---|
| `config/dapcode.php` | Konfigurasi file storage, rute excluded, dan modul. |
| `app/Services/Dapcode/InstallationService.php` | Pembuat & validator Installation ID unik persisten. |
| `app/Services/Dapcode/LicenseVerifier.php` | Verifikator tanda tangan digital asimetris RSA-2048 & SHA-256 Auth Token. |
| `app/Services/Dapcode/IntegrityService.php` | Pengecek checksum integritas data anti-tampering. |
| `app/Services/Dapcode/LicenseGuard.php` | Evaluator otorisasi sentral & auto-discovery modul dinamis. |
| `app/Services/Dapcode/ActivationService.php` | Handler aktivasi dan pencabutan lisensi (*Signed Revocation*). |
| `app/Http/Middleware/DapcodeLicenseMiddleware.php` | Middleware global fail-closed penghadang rute tak terotorisasi. |
| `app/Console/Commands/SignDapcodeLicense.php` | CLI Authority Signer resmi dengan verifikasi passcode. |
| `app/Http/Controllers/Dapcode/LicenseController.php` | Controller aktivasi, status, dan web signer terminal. |
| `resources/views/dapcode/activate.blade.php` | Tampilan form aktivasi visual. |
| `resources/views/dapcode/authority-terminal.blade.php` | Antarmuka web konsol Authority Signer Terminal. |
| `resources/views/dapcode/license-required.blade.php` | Tampilan pesan 403 Forbidden informatif. |
| `tests/Feature/DapcodeLicenseSecurityTest.php` | 22 skenario automated unit/feature tests. |
| `.agents/rules/security-guardian.md` | Aturan sistem penjaga keamanan agent 24 bab (*Bilingual*). |
