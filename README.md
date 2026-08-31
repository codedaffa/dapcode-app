# 🚀 DapCode App — Modular HMVC Portfolio & Developer Ecosystem

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel Framework">
  <img src="https://img.shields.io/badge/PHP-7.4%2B%20%7C%208.x-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite">
  <img src="https://img.shields.io/badge/Architecture-HMVC%20Modular-6366f1?style=for-the-badge" alt="HMVC">
  <img src="https://img.shields.io/badge/Security%20Engine-DapCode%20AegisGuard%E2%84%A2-emerald?style=for-the-badge&logo=auth0&logoColor=white" alt="DapCode AegisGuard">
  <img src="https://img.shields.io/badge/Theme%20Engine-Indonesian%20Holidays-dc2626?style=for-the-badge" alt="Indonesian Holidays">
  <img src="https://img.shields.io/badge/Localization-ID%20%7C%20EN-38bdf8?style=for-the-badge" alt="i18n">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License">
</p>

<p align="center">
  <img src="docs/images/dapcode-aegisguard.jpg" alt="DapCode AegisGuard - Advanced Cybersecurity & Protection" width="100%">
</p>

**DapCode App** adalah platform portofolio digital dan aplikasi ekosistem pengembang modern yang dibangun menggunakan framework **Laravel** dengan arsitektur modular **HMVC (Hierarchical Model-View-Controller)**, frontend asset pipeline modern bertenaga **Laravel Vite**, dan sistem keamanan lisensi asimetris **DapCode AegisGuard™** (*Asymmetric Cryptographic RSA-2048 & Fail-Closed Module Activation Engine*).

---

## 🌟 Fitur Utama (Key Features)

### 1. 🏛️ Arsitektur Modular HMVC & Dynamic Dispatcher
Seluruh fitur dikelompokkan dalam modul independen di dalam direktori `app/Modules/`:
- **Model, View, Controller, dan Route** tersendiri untuk setiap modul.
- *Dynamic Auto-Dispatcher* (`HMVC.php` & `HMVCServiceProvider.php`) yang meresolusi modul, sub-controller, action, dan parameter URL secara otomatis.
- *Hierarchical Sub-Requests* via helper `hmvc('ModuleName@action', $params)` untuk merender widget antar-modul secara terisolasi.
- *Module Scoped Rendering* via `$this->moduleRender('viewName', $data)` di Base Controller.

### 2. 🛡️ DapCode AegisGuard™ (Asymmetric License & Module Activation Engine)
Sistem proteksi dan perizinan modul tingkat tinggi berbasis kriptografi kunci asimetris murni (**RSA-2048 + SHA-256**):
- **Asymmetric Cryptographic Separation:** Aplikasi klien hanya memiliki *Public Verification Key*. *Private Signing Key* disimpan secara terisolasi di sisi Owner / DapCode License Authority Server.
- **Zero Secrets in Repository:** Tidak ada secret, private key, maupun fallback credentials di repositori aplikasi yang didistribusikan.
- **Persistent Installation ID:** Setiap instalasi memiliki ID unik persisten (`DAP-XXXXXX-...`) yang di-generate otomatis saat setup pertama.
- **Dynamic Module Auto-Discovery:** Sistem otomatis mendeteksi modul baru di `app/Modules/` tanpa perlu konfigurasi rute manual. Modul baru langsung terlindungi oleh AegisGuard.
- **Multi-Module Granular Control:** Lisensi dapat mengaktifkan modul spesifik (`commerce`, `research`, `career`, dll.) atau seluruh modul (`*`).
- **Fail-Closed Integrity Check:** Jika file lisensi diubah secara ilegal atau dirusak, sistem otomatis mengunci protected module secara aman tanpa merusak database.
- **Dedicated Authority Signer Tools:**
  - **Artisan CLI:** `php artisan dapcode:sign-license` untuk menandatangani lisensi dan token pencabutan via terminal.
  - **Authority Web Terminal:** `GET /dapcode/terminal` — Antarmuka web konsol interaktif bertema dark cybersecurity untuk generate signed payload dengan 1-klik.
- **Dedicated Activation Endpoints:**
  - `GET /dapcode/activate`: Antarmuka aktivasi visual dengan tombol salin Installation ID & shortcut ke Authority Terminal.
  - `POST /dapcode/activate`: Endpoint verifikasi payload lisensi.
  - `POST /dapcode/deactivate`: Endpoint pencabutan lisensi resmi dengan Signed Revocation Token.
  - `GET /dapcode/status`: Endpoint JSON status instalasi & lisensi.
  - `GET /dapcode/terminal`: Konsol Authority Web Terminal & HSM Signer.
  - `POST /dapcode/terminal/sign`: Endpoint penandatanganan payload asimetris RSA-2048.

### 3. ⚡ Modern Frontend Asset Pipeline (Laravel Vite)
- Ditenagai **Vite** & **laravel-vite-plugin** dengan kompilasi super cepat dan *Hot Module Replacement* (HMR).
- Integrasi Blade native melalui directive `@vite(['resources/css/app.css', 'resources/js/app.js'])`.
- Modul JavaScript modern berbasis standard ES Modules (ESM).
- Build asset teroptimasi dan versi otomatis di `public/build/`.

### 4. 🇮🇩 Indonesian Holiday & Celebration Theme Engine
Sistem tema dinamis yang otomatis mendeteksi kalender hari besar nasional Indonesia dengan arsitektur *Single Source of Truth*:
- **HUT Kemerdekaan RI (17 Agustus):** Merah Putih, font *Cinzel*, glow kemerdekaan.
- **Hari Raya Idul Fitri & Ramadhan:** Emerald & Gold, font *Amiri*, ornamen islami.
- **Tahun Baru Imlek:** Imperial Crimson & Gold, font *Playfair Display*.
- **Hari Raya Natal & Tahun Baru:** Pine Green & Crimson Snow.
- **Hari Lahir Pancasila (1 Juni):** Garuda Maroon & Gold.
- **Hari Sumpah Pemuda (28 Oktober):** Flame Orange & Crimson.
- **Hari Pahlawan (10 November):** Amber & Bronze.
- **Hari Kartini (21 April):** Orchid Pink & Lavender.
- **Hari Raya Waisak:** Saffron Gold & Lotus.
- **Tahun Baru Masehi:** Cosmic Violet & Neon Cyan, font *Space Grotesk*.
- **Manual Selector:** Pengguna dapat mengganti atau mempratinjau tema secara manual melalui ikon palet di header (`/theme/{key}` atau `/theme/auto`).

### 5. 🌐 Dual-Language Localization (ID / EN)
- Dukungan penuh multi-bahasa untuk seluruh modul dan elemen sistem.
- Beralih bahasa secara instan melalui switcher di header (`/lang/id` & `/lang/en`).

### 6. 🧭 Navigasi Interaktif & Header Title Module Switcher
- **Header Title Dropdown:** Klik pada judul header (`<h1 class="header-title">`) untuk membuka direktori 13 modul HMVC secara instan.
- **Contextual Sidebar:** Sidebar fokus khusus menampilkan sub-menu modul yang sedang aktif tanpa elemen yang menumpuk.
- **No-Scrollbar Design:** Tampilan rapi bebas scrollbar yang mengganggu.

---

## 📂 13 Modul HMVC Terintegrasi

| # | Modul | Kategori | Akses Awal (Fresh Install) | Rute Utama |
|---|---|---|---|---|
| 1 | **Dashboard** | Core System | **Free / Public** | `/dashboard` |
| 2 | **Profile** | Bio & Identitas | **Free / Public** | `/profile` |
| 3 | **Education** | Akademik | **Free / Public** | `/education` |
| 4 | **Certification** | Lisensi Profesi | **Protected Module** | `/certification` |
| 5 | **Achievement** | Prestasi & Penghargaan | **Protected Module** | `/achievement` |
| 6 | **Interest** | Bidang Minat | **Protected Module** | `/interest` |
| 7 | **Project** | Portofolio Proyek | **Protected Module** | `/project` |
| 8 | **Research** | Riset & Publikasi | **Protected Module** | `/research` |
| 9 | **Career** | Rekam Jejak Kerja | **Protected Module** | `/career` |
| 10 | **Activity** | Komunitas & Organisasi | **Protected Module** | `/activity` |
| 11 | **Media** | Galeri Multimedia | **Protected Module** | `/media` |
| 12 | **Commerce** | Katalog Produk & Layanan | **Protected Module** | `/commerce` |
| 13 | **Setting** | Konfigurasi Sistem | **Protected Module** | `/setting` |

---

## 🛠️ Struktur Direktori Proyek

```text
dapcode-app/
├── app/
│   ├── Console/Commands/
│   │   └── SignDapcodeLicense.php     # Authority CLI Signer (RSA-2048 Private Key)
│   ├── Helpers/
│   │   └── hmvc.php                   # Helper global: hmvc(), module_view(), holiday_theme()
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php         # Base Controller dengan moduleRender() & json helper
│   │   │   ├── HMVCController.php     # Dynamic Route Request Dispatcher
│   │   │   ├── Dapcode/
│   │   │   │   └── LicenseController.php # Activation & Deactivation Handlers
│   │   │   └── Core/                  # Base Controllers per Modul
│   │   └── Middleware/
│   │       └── DapcodeLicenseMiddleware.php # Protected Module License Guard Middleware
│   ├── Modules/                       # 13 Modul HMVC Terisolasi
│   │   ├── Dashboard/
│   │   ├── Profile/
│   │   ├── Project/
│   │   ├── Commerce/
│   │   └── ... (13 Modules)
│   ├── Providers/
│   │   ├── AppServiceProvider.php     # Registrasi Directive Blade @vite
│   │   └── HMVCServiceProvider.php    # Dynamic HMVC Route & View Namespace Loader
│   └── Services/
│       ├── Dapcode/                   # Client License & Activation Engine
│       │   ├── InstallationService.php # Persistent Installation ID Generator
│       │   ├── LicenseVerifier.php    # Asymmetric RSA Digital Signature Verification
│       │   ├── ActivationService.php  # Activation & Signed Revocation Handlers
│       │   ├── LicenseGuard.php       # Centralized Module Access Guard
│       │   └── IntegrityService.php   # Tamper Detection & State Checksums
│       ├── HMVC/
│       │   └── HMVC.php               # Core Dispatcher & Hierarchical Request Engine
│       ├── Theme/
│       │   └── HolidayThemeService.php # Celebration Theme Registry & Date Detector
│       └── Vite/
│           └── ViteHelper.php         # Vite Tag Resolver (Dev Server / Production Build)
├── config/
│   └── dapcode.php                    # License & Protected Modules Configuration
├── docs/
│   └── security/
│       ├── dapcode-threat-model.md    # Threat Model & Trust Boundaries
│       └── dapcode-license-architecture.md # Architecture & Hardening Guide
├── public/
│   ├── build/                         # Output Asset Terkompilasi Vite (Production)
│   │   ├── assets/
│   │   └── manifest.json
│   └── assets/                        # Static Assets (Fallback / Icons)
├── resources/
│   ├── css/
│   │   ├── app.css                    # Entry Point CSS Utama
│   │   ├── holiday-themes.css         # CSS 10 Tema Hari Raya
│   │   └── theme-responsive.css       # Core Design System & Responsive Layout
│   ├── js/
│   │   ├── app.js                     # Entry Point JS Utama (ESM)
│   │   ├── bootstrap.js               # Axios & Lodash Setup
│   │   └── theme-responsive.js        # Sidebar, Modal & Dropdown Controller
│   ├── lang/
│   │   ├── id/                        # Bahasa Indonesia Translation
│   │   └── en/                        # English Translation
│   └── views/
│       ├── dapcode/                   # Activation & License Required Views
│       │   ├── activate.blade.php
│       │   └── license-required.blade.php
│       ├── portfolio.blade.php        # Landing Page Portofolio (/)
│       └── theme/                     # Master Layout, Header, Sidebar, Footer
├── storage/app/dapcode/               # Private Runtime License Storage (.gitignore)
├── tests/
│   └── Feature/
│       └── DapcodeLicenseSecurityTest.php # 22 Automated Security & Verification Tests
├── package.json                       # Vite Scripts & Dependencies
├── vite.config.js                     # Konfigurasi Build Laravel Vite
└── routes/
    └── web.php                        # Root Web Routes & DapCode Endpoints
```

---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

### 1. Kloning Repositori
```bash
git clone https://github.com/codedaffa/dapcode-app.git
cd dapcode-app
```

### 2. Instal Dependensi Composer & NPM
```bash
composer install
npm install
```

### 3. Konfigurasi Lingkungan (.env)
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Build Frontend Asset (Vite)

**Mode Development (Live Hot-Reload):**
```bash
npm run dev
```

**Mode Production (Build & Minify):**
```bash
npm run build
```

### 5. Menjalankan Server Laravel
```bash
php artisan serve
```
Buka browser pada: **`http://127.0.0.1:8000`**

---

---

## 🔑 Panduan Men-generate Signed License Payload (Authority)

Sebagai pemilik kode (*Owner / DapCode License Authority*), Anda dapat men-generate payload lisensi bertanda tangan kriptografis (**RSA-2048**) untuk Installation ID klien menggunakan salah satu dari cara berikut:

### 1. 🖥️ Menggunakan Authority Web Terminal (Paling Visual & Praktis)

Buka URL: **`http://127.0.0.1:8000/dapcode/terminal`**
1. Pilih Mode: **Sign License** (Aktivasi) atau **Sign Revocation** (Pencabutan).
2. Masukkan **Authority Secret Passcode** (`***********`).
3. Masukkan **Target Installation ID** (tersedia tombol *"Gunakan ID Mesin Ini"*).
4. Tentukan masa berlaku lisensi (1, 2, 5, atau 10 tahun).
5. Pilih otorisasi modul: **Semua Modul (`*`)** atau checklist per-modul.
6. Klik **"Generate & Sign Payload (RSA-2048)"** &rarr; Salin output JSON langsung dengan tombol **"Salin JSON"**!

---

### 2. ⚡ Menggunakan Artisan Command (Terminal CLI)

Command ini mewajibkan **Authority Secret Passcode** (`--passcode="***********"`) atau akan meminta input rahasia di terminal:

#### A. Generate Lisensi Penuh (Semua Modul `*`, Berlaku 2 Tahun):
```bash
php artisan dapcode:sign-license DAP-ECEAG3-ad057ba3-0267-4bb4-8b64-5d0e697ef2ce --passcode="***********"
```

#### B. Generate Lisensi Parsial (Hanya Modul Tertentu):
```bash
php artisan dapcode:sign-license DAP-ECEAG3-ad057ba3-0267-4bb4-8b64-5d0e697ef2ce --modules=commerce --modules=project --passcode="***********"
```

#### C. Menentukan Durasi Masa Berlaku (misal 5 Tahun):
```bash
php artisan dapcode:sign-license DAP-ECEAG3-ad057ba3-0267-4bb4-8b64-5d0e697ef2ce --years=5 --passcode="***********"
```

#### D. Generate Signed Revocation Token (Pencabutan Seluruh Lisensi):
```bash
php artisan dapcode:sign-license --revoke --license_id=LIC-2026-PRO-2F2F12 --passcode="***********"
```

#### E. Generate Signed Granular Revocation Token (Pencabutan Modul Tertentu Saja):
```bash
php artisan dapcode:sign-license --revoke --license_id=LIC-2026-PRO-2F2F12 --modules=commerce --passcode="***********"
```

---

### 3. 🧩 Perilaku Keamanan Saat Siklus Hidup Modul (Module Lifecycle)

Sistem proteksi DapCode mendukung penambahan, perubahan, dan penghapusan modul secara *zero-config*:

* **Menambah Modul Baru (`app/Modules/NamaModul/`):**
  - **Otomatis Terproteksi:** Sistem langsung memindai modul baru.
  - Jika klien memiliki lisensi penuh (`*`), modul baru **langsung aktif dan dapat dibuka**.
  - Jika klien memiliki lisensi parsial (misal hanya `commerce`), modul baru **otomatis terkunci (HTTP 403)** sampai didaftarkan ke lisensi klien.
* **Menghapus Modul:**
  - **Aman & Bebas Error:** Tidak merusak validitas lisensi maupun tanda tangan digital. Rute modul yang dihapus akan merespons dengan `404 Not Found` standar yang rapi.
* **Mengubah Nama Modul (Rename):**
  - Jika lisensi klien adalah `*`, modul dengan nama baru langsung aktif otomatis.
  - Jika lisensi klien adalah parsial khusus, modul dengan nama baru akan terkunci sampai lisensi baru diterbitkan.

---

### 4. 💻 Menggunakan Raw PHP di Terminal (Standalone Server)

Jika Anda menjalankan penandatanganan dari server terpisah atau tanpa framework Laravel, Anda dapat menjalankan perintah PHP langsung di terminal:

#### A. Manual Generate Signed Activation License:
```bash
php -r "
\$privKey = file_get_contents('/path/to/authority_private_key.pem');
\$authHash = hash('sha256', '***********');
\$licenseId = 'LIC-' . date('Y') . '-PRO-' . strtoupper(bin2hex(random_bytes(3)));
\$instId = 'DAP-ECEAG3-ad057ba3-0267-4bb4-8b64-5d0e697ef2ce';
\$payload = [
    'license_id'      => \$licenseId,
    'installation_id' => \$instId,
    'status'          => 'ACTIVE',
    'issued_at'       => date('c'),
    'expires_at'      => date('c', strtotime('+2 years')),
    'modules'         => ['*'],
    'auth_token'      => hash('sha256', \$authHash . ':' . \$licenseId . ':' . \$instId),
];
\$clean = \$payload;
ksort(\$clean);
if (isset(\$clean['modules']) && is_array(\$clean['modules'])) sort(\$clean['modules']);
\$canonical = json_encode(\$clean, JSON_UNESCAPED_SLASHES);
openssl_sign(\$canonical, \$sig, \$privKey, OPENSSL_ALGO_SHA256);
\$payload['signature'] = base64_encode(\$sig);
echo json_encode(\$payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
"
```

#### B. Manual Generate Signed Full Revocation Token:
```bash
php -r "
\$privKey = file_get_contents('/path/to/authority_private_key.pem');
\$authHash = hash('sha256', '***********');
\$licenseId = 'LIC-2026-PRO-XXXXXX';
\$instId = 'DAP-ECEAG3-ad057ba3-0267-4bb4-8b64-5d0e697ef2ce';
\$payload = [
    'action'          => 'REVOKE',
    'license_id'      => \$licenseId,
    'installation_id' => \$instId,
    'revoked_at'      => date('c'),
    'reason'          => 'Manual Revocation by Authority',
    'auth_token'      => hash('sha256', \$authHash . ':' . \$licenseId . ':' . \$instId . ':REVOKE'),
];
\$clean = \$payload;
ksort(\$clean);
\$canonical = json_encode(\$clean, JSON_UNESCAPED_SLASHES);
openssl_sign(\$canonical, \$sig, \$privKey, OPENSSL_ALGO_SHA256);
\$payload['signature'] = base64_encode(\$sig);
echo json_encode(\$payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
"
```

---

### 5. Struktur JSON Payload yang Dihasilkan

Output dari perintah di atas menghasilkan payload JSON resmi yang siap disalin dan ditempelkan ke form aktivasi atau form pencabutan di `http://127.0.0.1:8000/dapcode/activate`:

#### Contoh Output Activation License Payload:
```json
{
    "license_id": "LIC-2026-PRO-2F2F12",
    "installation_id": "DAP-ECEAG3-ad057ba3-0267-4bb4-8b64-5d0e697ef2ce",
    "status": "ACTIVE",
    "issued_at": "2026-08-29T11:07:23+00:00",
    "expires_at": "2028-08-29T11:07:23+00:00",
    "modules": [
        "*"
    ],
    "auth_token": "4d707fa9f66e7906cb4f81f134ba19a2dc56f3bbe57a7f6b9c2898213c9729ee",
    "signature": "bxrvQe7ujBnf6aK2qLiExMBL0RUakgeqHB4/Dv3NlZBq1+u8rV0jyEJnXX3TwxCrmMuIzhCkM0FP/daUMQCRdwaO5cXjSStW3rI7kgDRrTIZEDAth0kyonKJBn+tAxxTJ+zRAgsYBP+No66J1wfuFgwVIqpc8p831a7d5Emjtn1u02LK+jOIEfNJZo0Vy0ak7sVRtsQfbkKpZ0WQkdKCQTuS/gxbbE/l1nkuv+izCpXsxQpzhh8vWUcpvOOKA9Htp7ebvY/8WbDed/8j5xv26JfxJ3SYoeyuPqxb5DpKuIpvAZQcAr8syVJxLsQ76KPF2kLDTpUDt1j3JM43Wjwqzg=="
}
```

#### Contoh Output Signed Granular Revocation Token:
```json
{
    "action": "REVOKE",
    "license_id": "LIC-2026-PRO-2F2F12",
    "installation_id": "DAP-ECEAG3-ad057ba3-0267-4bb4-8b64-5d0e697ef2ce",
    "revoked_at": "2026-08-29T11:40:39+00:00",
    "reason": "Manual Revocation by Authority",
    "revoked_modules": [
        "commerce"
    ],
    "auth_token": "8f3b145a6c38e9182390a78b548b105a39cb68d18471f49a37e19da37281ef12",
    "signature": "AQrvHIkKlaG+YS6F27kxHYGyx5KMYhRWtabofvMKYUOJ4ryI8ix4emK//r7fc8MlGrS7GbH97KBTWQtTEIm5Pl4NIuAOHRxoPM8iYtlI4f4E+JVHPW11FeUnor8eknAEib05W3NSumD2/bvIIue+ja35wqu1zQXIN7Ab8W8z9YO6hGLJoA+1PDrCNIfGlN0iBlnm+UrubTkPm6S2bGXRoXXuTbd/kv+uLPUVvKYtlKuPjeSUt2X6wz6uVbYvwUS5EVKFWvqcEIik8TmMERHVx2BH4Kn2oWJP0Su9O4Hr15ZJlUvhbsq6cXC/glytqTNU3N1RalqnH7bVWpwzBeUx6w=="
}
```

---

### 6. Penjelasan Atribut Payload

| Atribut | Tipe | Keterangan |
|---|---|---|
| `license_id` | `string` | ID unik lisensi yang diterbitkan Authority (misal `LIC-2026-PRO-XXXXXX`). |
| `installation_id` | `string` | ID instalasi klien tujuan lisensi (`DAP-XXXXXX-...`) atau `*` untuk wildcard. |
| `status` | `string` | Status lisensi (`ACTIVE`, `REVOKED`, `SUSPENDED`). |
| `issued_at` | `string` | Waktu lisensi diterbitkan dalam format ISO-8601 UTC. |
| `expires_at` | `string` | Waktu kedaluwarsa lisensi dalam format ISO-8601 UTC. |
| `modules` | `array` | Daftar modul yang diizinkan (misal `["*"]` atau `["commerce", "project"]`). |
| `auth_token` | `string` | Token otorisasi terenkripsi turunan dari Master Secret Passcode (`***********`). |
| `signature` | `string` | Tanda tangan digital RSA-SHA256 (Base64) dari canonical JSON payload. |

---

## 🧪 Panduan Pengujian (Testing Guide)

### 👨‍💻 Skenario A: Sebagai Programmer Clone (Klien)
1. **Akses Aplikasi (Fresh Clone):** Buka `http://127.0.0.1:8000/` atau `/commerce` &rarr; **Terkunci Otomatis (HTTP 403 Forbidden - Fail Closed)**.
2. **Buka Halaman Aktivasi:** Akses `http://127.0.0.1:8000/dapcode/activate` &rarr; **Terbuka Bebas (HTTP 200)**.
3. **Ambil Installation ID:** Pada halaman aktivasi, klik tombol **"Salin ID"** (contoh: `DAP-BCLUFC-xxxx`), dan kirimkan ke Pemilik Aplikasi.
4. **Aktivasi Lisensi:** Setelah menerima JSON lisensi dari Pemilik, tempelkan ke form aktivasi dan klik **Verifikasi & Aktivasi** &rarr; Halaman utama dan modul yang diizinkan langsung terbuka penuh!

---

### 👑 Skenario B: Sebagai Pemilik Aplikasi (License Authority Signer)
1. **Jalankan Command Signer:**
   ```bash
   php artisan dapcode:sign-license <INSTALLATION_ID_DARI_PROGRAMMER> --passcode="***********"
   ```
2. **Kirim Output JSON:** Salin JSON bertanda-tangan digital tersebut dan kirimkan ke programmer untuk dimasukkan ke form aktivasi.
3. **Mencabut Lisensi (Revokasi):**
   ```bash
   php artisan dapcode:sign-license --revoke --license_id=<LICENSE_ID> --passcode="***********"
   ```
   Salin JSON Revocation Token dan masukkan ke form **"Pencabutan / Deaktivasi Lisensi"** di `/dapcode/activate`.

---

## 🏆 🎯 RED TEAM & SECURITY AUDIT CHALLENGE (For Fun & Learning)

> *"In cryptography, trust is built upon verifiable mathematics, not obscurity."*

Kami mengundang **seluruh Software Engineer, Security Researcher, dan Red Teamers** untuk mengaudit dan menguji ketahanan arsitektur kriptografis **DapCode License Engine** ini!

> 📌 *Catatan: Tantangan ini bersifat non-komersial (tidak berhadiah), ditujukan murni sebagai sarana hiburan, eksplorasi teknis, dan media pembelajaran bersama seputar arsitektur keamanan aplikasi web.*

### 🎯 Ruang Lingkup Audit & Pengujian:
- **Asymmetric Cryptography:** Integritas penandatanganan dan verifikasi tanda tangan digital (**RSA-2048 SHA-256**).
- **Payload Integrity:** Keamanan kanonikalisasi data, hash token otorisasi, dan deteksi tampering.
- **Granular Access Control:** Ketepatan otorisasi per modul serta validasi token pencabutan lisensi (*Signed Revocation*).
- **Fail-Closed Enforcement:** Konsistensi penolakan akses (HTTP 403) pada seluruh anomali request dan data lisensi.

> 💡 **Menemukan celah atau kelemahan kriptografis?**  
> Laporkan temuan Proof-of-Concept (PoC) atau buat *Pull Request* untuk berkontribusi dalam menyempurnakan standar keamanan project ini!

---

## 🧪 Menjalankan Automated Security Tests

Untuk memvalidasi 22 skenario pengujian keamanan lisensi (anti-pemalsuan, anti-tampering, granularitas modul, revocation, fail-closed):

```bash
php vendor/phpunit/phpunit/phpunit tests/Feature/DapcodeLicenseSecurityTest.php
```

---

## 🎨 Menyesuaikan Tema Hari Raya (Theme Switcher)

Anda dapat menguji dan mengganti tema perayaan secara langsung melalui URL:
- **Otomatis (Berdasarkan Kalender):** `http://127.0.0.1:8000/theme/auto`
- **HUT Kemerdekaan RI:** `http://127.0.0.1:8000/theme/kemerdekaan`
- **Hari Raya Idul Fitri:** `http://127.0.0.1:8000/theme/idulfitri`
- **Tahun Baru Imlek:** `http://127.0.0.1:8000/theme/imlek`
- **Hari Raya Natal & Tahun Baru:** `http://127.0.0.1:8000/theme/natal`
- **Hari Lahir Pancasila:** `http://127.0.0.1:8000/theme/pancasila`
- **Hari Sumpah Pemuda:** `http://127.0.0.1:8000/theme/pemuda`
- **Hari Pahlawan:** `http://127.0.0.1:8000/theme/pahlawan`
- **Hari Kartini:** `http://127.0.0.1:8000/theme/kartini`
- **Hari Raya Waisak:** `http://127.0.0.1:8000/theme/waisak`
- **Tahun Baru Masehi:** `http://127.0.0.1:8000/theme/tahunbaru`

---

## 📄 Lisensi (License)

Aplikasi ini bersifat *open-source* di bawah lisensi [MIT License](LICENSE).  
Dikembangkan dengan ❤️ oleh **[DapCode Studio](https://github.com/codedaffa)**.
