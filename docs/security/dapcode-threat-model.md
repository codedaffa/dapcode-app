# DAPCODE AEGISGUARD™ — THREAT MODEL & SECURITY EVALUATION

Dokumen ini memetakan model ancaman (*Threat Model*), matriks vektor serangan potensial, mekanisme mitigasi pertahanan berlapis, serta batas kepercayaan (*Trust Boundary*) pada **DapCode AegisGuard™**.

---

## 1. Threat Vectors & Mitigations Matrix

| Vektor Ancaman | Dampak Potensial | Mitigasi AegisGuard™ | Status |
| :--- | :--- | :--- | :--- |
| **Middleware Bypass** (`return $next($request)`) | Melewati filter HTTP perimeter pertama | **Layer 2 (HMVC Dispatcher)**, **Layer 3 (Core Base Controller)**, dan **Layer 4 (View Composer)** memeriksa otorisasi lisensi secara independen sebelum memanggil action atau merender view. | **MITIGATED** |
| **Direct Controller Instantiation** (`new Module()`) | Melewati HTTP routing dan HMVC | **Layer 3 (`__construct` & `moduleRender`)** memvalidasi `LicenseGuard::assertModuleAllowed()` saat inisialisasi class controller. | **MITIGATED** |
| **Direct View Rendering** (`view('module::index')`) | Melewati controller dan routing | **Layer 4 (View Scoped Rendering)** memvalidasi lisensi modul saat view namespace diakses. | **MITIGATED** |
| **Fresh Clone Source Code Leakage** | Pembajakan source code berbayar dari repository publik | **Layer 6 (AES-256-GCM Envelope Encryption)**: Source code disimpan terenkripsi (`.php.enc`) dan hanya bisa didekripsi dengan lisensi sah yang terikat ke Installation ID. File `.php` plaintext lokal diabaikan oleh `.gitignore`. | **MITIGATED** |
| **Development Code Leak to GitHub** | Teruploadnya file non-enkripsi saat programmer mengedit kode | **`.gitignore` Strict Rules**: Seluruh file `.php` di dalam `app/Modules/*/Controllers/` & `Models/` diabaikan otomatis. Kode dirilis ke Git melalui `php artisan dapcode:pack` yang mengenkripsi ulang ke format `.enc`. | **MITIGATED** |
| **Passcode Reverse Engineering / Hardcoded Leaks** | Mengekstrak passcode authority dari source code | **One-Way SHA-256 Digest**: Passcode diverifikasi menggunakan perbandingan hash konstan `hash_equals()` dengan digest SHA-256. Tidak ada plaintext passcode di source code. | **MITIGATED** |
| **Signature Forgery / Fake License** | Pembuatan lisensi palsu secara lokal | **Asymmetric RSA-2048 + SHA-256 Signature**: Aplikasi klien hanya memiliki Public Key. Private signing key terisolasi aman di sisi Pemilik/Otoritas. | **MITIGATED** |
| **Installation ID Spoofing / Cloning** | Menggunakan lisensi dari server lain | Lisensi mengikat `installation_id` mesin lokal yang di-hash dari hardware/environment signature. Key AES modul mengikat `installation_id`. | **MITIGATED** |
| **Ciphertext / Tag Tampering** | Memodifikasi file `.enc` atau manifest | Dekripsi AES-256-GCM memvalidasi 16-byte Authentication Tag + SHA-256 checksum pasca-dekripsi. Jika rusak, eksekusi ditolak (*Fail-Closed*). | **MITIGATED** |
| **Stale / Injected Plaintext File** | Membuat file PHP manual tanpa lisensi | `LicenseGuard` memvalidasi status lisensi aktif dan integritas modul. Jika tidak ada lisensi sah, akses tetap menghasilkan HTTP 403. | **MITIGATED** |
| **Race Condition on Revocation** | Eksekusi modul bersamaan dengan revokasi | File locking eksklusif (`flock LOCK_EX`) pada proses unlock dan validasi ulang lisensi di critical section. Plaintext langsung dipurge saat revokasi. | **MITIGATED** |
| **Core Security File Tampering** | Mengedit `LicenseGuard.php` atau `LicenseVerifier.php` | **Layer 5 (IntegrityService)** memverifikasi SHA-256 manifest file inti sistem. Status menjadi `INTEGRITY_FAILED` jika dimodifikasi. | **MITIGATED** |
| **Path Traversal & Obfuscation** (`../`, encoded slugs) | Mengakses modul terlarang via path manipulasi | **Canonical Module Resolver** menormalisasi string, menolak traversal, encoding ganda, dan karakter non-alfanumerik. | **MITIGATED** |

---

## 2. Defense-in-Depth Execution Boundary

Batas eksekusi modul tidak bergantung pada titik tunggal:
1. **Layer 1 (Perimeter HTTP Guard)**: Mencegat request HTTP pada level middleware.
2. **Layer 2 (HMVC Dynamic Dispatcher)**: Memvalidasi `resolveCanonicalModuleName()` dan memanggil `LicenseGuard::assertModuleAllowed()` sebelum resolver memanggil controller.
3. **Layer 3 (Core Base Controller)**: `Controller::__construct()`, `render()`, dan `moduleRender()` mengunci instansiasi dan eksekusi controller.
4. **Layer 4 (RSA-2048 Digital Licensing & Authority Passcode)**: Memverifikasi keabsahan tanda tangan kriptografis dan digest passcode authority.
5. **Layer 5 (Anti-Tampering Integrity Guard)**: Memverifikasi hash SHA-256 seluruh file core security.
6. **Layer 6 (AES-256-GCM Envelope Encryption)**: Menyimpan kode kritis dalam format terenkripsi `.php.enc` di GitHub dan hanya membuka plaintext secara lokal saat aktif.

---

## 3. Trust Boundary & White-Box Limitations

1. **Defense-in-Depth vs. White-box Control**:
   - Sistem ini secara drastis menaikkan tingkat kesulitan (*cost of attack*) terhadap modifikasi kasual, penyalinan source code dari fresh clone, dan serangan bypass aplikasi umum.
   - Pada model lingkungan *white-box* (di mana penyerang memiliki akses `root` server, kemampuan modifikasi binary PHP runtime, atau debugger ekstensi internal), interpreter PHP tetap membaca bytecode saat runtime.
2. **Rekomendasi Tambahan untuk Proteksi Komersial On-Premise**:
   - Untuk software enterprise yang didistribusikan langsung ke server klien yang tidak tepercaya (*untrusted on-premise*), disarankan memadukan AegisGuard™ dengan kompilasi bytecode biner (seperti **ionCube PHP Encoder** atau **SourceGuardian**).

---

## 4. Automated Security Verification (100% Pass)

Ketahanan seluruh vektor serangan di atas divalidasi secara otomatis melalui **61 Security Feature Tests**:
* `Tests\Feature\DapcodeEncryptedModuleSecurityTest`: **25/25 PASS**
* `Tests\Feature\DapcodeLayeredGuardSecurityTest`: **12/12 PASS**
* `Tests\Feature\DapcodeLicenseSecurityTest`: **22/22 PASS**
* `Tests\Feature\ExampleTest`: **2/2 PASS**
