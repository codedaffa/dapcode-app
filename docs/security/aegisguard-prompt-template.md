# 🛡️ DAPCODE AEGISGUARD™ — MASTER IMPLEMENTATION PROMPT TEMPLATE

> **Panduan Penggunaan:** Salin seluruh isi template prompt di bawah ini dan tempelkan (*copy-paste*) ke AI Coding Assistant (Antigravity, Claude, ChatGPT, Cursor, dll.) pada proyek Laravel baru Anda untuk mengimplementasikan **DapCode AegisGuard™** secara otomatis dan terstandarisasi.

---

```markdown
# 🛡️ SYSTEM PROMPT: IMPLEMENTASI DAPCODE AEGISGUARD™ SECURITY ENGINE

Anda bertindak sebagai **Principal Security Architect & Senior Laravel Engineer**.

Tugas Anda adalah mengimplementasikan **DapCode AegisGuard™ (6-Layer Defense-in-Depth, Asymmetric RSA-2048 Digital Licensing & AES-256-GCM Envelope Encryption Engine)** secara lengkap, tangguh, dan siap produksi ke dalam repositori aplikasi Laravel ini.

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
5. **AES-256-GCM Envelope Encryption & Fresh Clone Protection:**
   - Source code controller & model tersimpan dalam format terenkripsi `.php.enc` di GitHub.
   - Plaintext `.php` hasil aktivasi lokal otomatis diabaikan oleh `.gitignore`.
6. **Anti-Tampering & Granular Signed Revocation:**
   - Setiap modifikasi ilegal pada file lisensi atau file security inti sistem langsung memicu penguncian otomatis (Layer 5 Integrity).
   - Pencabutan lisensi (penuh maupun per-modul) wajib diverifikasi menggunakan *Signed Revocation Token* bertanda tangan RSA-2048 yang sah dan langsung menghapus plaintext dari disk.

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

3. **`ModuleEncryptionService.php`:**
   - Enkripsi dan dekripsi modul amplop AES-256-GCM dengan HKDF-SHA256 key derivation.
   - `encryptModule($module, $license)`: Membaca `.php` dan mengenkripsinya ke `.php.enc` dan `manifest.json`.
   - `decryptModule($module, $license)`: Mendekripsi `.php.enc` ke `.php` sementara dengan validasi GCM Tag dan Checksum.
   - `lockModule($module)`: Menghapus file `.php` dari disk (kembali ke locked state).

4. **`IntegrityService.php`:**
   - Mengelola checksum integritas SHA-256 file core (`integrity_manifest.json`) dan state lisensi lokal (`.license-state`).

5. **`LicenseGuard.php`:**
   - Central evaluator dengan in-memory request caching:
     - `canAccessApplication()`: Mengecek integritas, status ACTIVE, masa berlaku, dan validasi signature lisensi global.
     - `isModuleAllowed(string $moduleName)`: Mengecek apakah modul tertentu diizinkan dan belum dicabut.
     - `assertModuleAllowed(string $moduleName)`: Memvalidasi dan melempar `HttpResponseException(403)` jika modul terkunci.
     - `getAllAvailableModules()`: Memindai dinamis direktori `app/Modules/`.

6. **`ActivationService.php`:**
   - `activate($licensePayload)`: Memverifikasi tanda tangan digital RSA-2048 payload lisensi baru, mencocokkan Installation ID, menyimpan ke `.license`, mendiskripsi modul yang diotorisasi, dan mengupdate integrity checksum.
   - `deactivate($revocationInput, $reason)`: Memverifikasi *Signed Revocation Token*, memproses pencabutan penuh atau parsial, menghapus plaintext modul yang dicabut, dan memperbarui lisensi.

---

### C. Console Commands (`app/Console/Commands/`)

1. **`MakeHMVCModule.php` (`php artisan make:module {name}`):**
   - Membuat Controller, Model, Blade View, Core Base Controller, otomatis memaketkan enkripsi `.php.enc`, dan mengunci modul dalam status fresh clone.
2. **`DapcodePackCommand.php` (`php artisan dapcode:pack {module=all}`):**
   - Mengemas dan mengenkripsi ulang source code `.php` terbaru ke dalam amplop `.php.enc` sebelum commit ke Git.
3. **`DapcodeModuleCommand.php` (`php artisan dapcode:module`):**
   - Menampilkan tabel visual status enkripsi, ketersediaan plaintext, dan otorisasi lisensi seluruh modul.
4. **`SignDapcodeLicense.php` (`php artisan dapcode:sign-license`):**
   - Menandatangani lisensi digital dan token pencabutan via CLI.

---

### D. Global Middleware & Base Controller

1. **`DapcodeLicenseMiddleware.php`:**
   - Mengecualikan `excluded_routes`.
   - Memvalidasi `LicenseGuard::canAccessApplication()` dan `LicenseGuard::isModuleAllowed($targetModule)`.
2. **`app/Http/Controllers/Controller.php`:**
   - Constructor (`__construct`) memanggil `LicenseGuard::assertModuleAllowed()` pada setiap instansiasi controller.

---

### E. Authority Web Terminal & Views (`/dapcode/terminal`)

- **`authority-terminal.blade.php`:** Web Terminal interaktif dengan Artisan console runner, modal dialog **`+ Make Module`**, preset buttons, dan RSA-2048 Signer.
- **`activate.blade.php`:** Halaman aktivasi klien dengan tombol salin Installation ID dan modal konfirmasi pencabutan lisensi.
```
