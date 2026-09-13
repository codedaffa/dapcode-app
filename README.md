# 🚀 DapCode App — Modular HMVC Portfolio & Developer Ecosystem

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel Framework">
  <img src="https://img.shields.io/badge/PHP-7.4%2B%20%7C%208.x-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite">
  <img src="https://img.shields.io/badge/Architecture-HMVC%20Modular-6366f1?style=for-the-badge" alt="HMVC">
  <img src="https://img.shields.io/badge/Security%20Engine-DapCode%20AegisGuard%E2%84%A2-emerald?style=for-the-badge&logo=auth0&logoColor=white" alt="DapCode AegisGuard">
  <img src="https://img.shields.io/badge/Minifier%20Vault-Secure%20SourceMap-blueviolet?style=for-the-badge" alt="Secure SourceMap Vault">
  <img src="https://img.shields.io/badge/Theme%20Engine-Indonesian%20Holidays-dc2626?style=for-the-badge" alt="Indonesian Holidays">
  <img src="https://img.shields.io/badge/Localization-ID%20%7C%20EN-38bdf8?style=for-the-badge" alt="i18n">
  <img src="https://img.shields.io/badge/UI%20Design%20System-22%2B%20Components-6366f1?style=for-the-badge&logo=blueprint&logoColor=white" alt="UI Component Library">
  <img src="https://img.shields.io/badge/Security%20%26%20UI%20Tests-82%20Passed%20(100%25)-brightgreen?style=for-the-badge" alt="82 Passed Tests">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License">
</p>

<p align="center">
  <img src="docs/images/dapcode-aegisguard.jpg" alt="DapCode AegisGuard - Advanced Cybersecurity & Protection" width="100%">
</p>

**DapCode App** adalah platform portofolio digital dan ekosistem pengembang modern yang dibangun di atas framework **Laravel** dengan arsitektur modular **HMVC (Hierarchical Model-View-Controller)**, **Internal UI Component Library & Design System** (`<x-ui.*>`) siap pakai dengan interaktif showcase playground, frontend asset pipeline modern bertenaga **Laravel Vite**, sistem proteksi multi-lapis terenkripsi **DapCode AegisGuard™** (*6-Layer Defense-in-Depth, Asymmetric RSA-2048 Digital Licensing, and AES-256-GCM Envelope Encryption*), serta mesin **Code & View Minifier / Unminifier** bertenaga **Secure SourceMap Vault** (*Zero Plaintext Leak, 100% Byte-for-Byte SHA-256 Exact Restoration*).

---

## 🌟 Fitur Utama (Key Features)

### 1. 🏛️ Arsitektur Modular HMVC & Dynamic Dispatcher
Seluruh fitur dikelompokkan dalam modul independen di dalam direktori `app/Modules/`:
- **Model, View, Controller, dan Route** terisolasi rapi untuk setiap modul.
- *Dynamic Auto-Dispatcher* (`HMVC.php` & `HMVCServiceProvider.php`) yang meresolusi modul, sub-controller, action, dan parameter URL secara otomatis.
- *Hierarchical Sub-Requests* via helper `hmvc('ModuleName@action', $params)` untuk merender komponen antar-modul secara aman.
- *Module Scoped Rendering* via `$this->moduleRender('viewName', $data)` di Base Controller.

### 2. 🛡️ DapCode AegisGuard™ (6-Layer Defense-in-Depth Security)
Sistem keamanan enterprise yang menggabungkan kriptografi kunci asimetris (**RSA-2048 + SHA-256**) dan enkripsi amplop (**AES-256-GCM**) dengan arsitektur pertahanan berlapis:
- **Layer 1 (HTTP Middleware & Dynamic Route Guard):** Mencegat seluruh request HTTP menuju modul sebelum mencapai controller.
- **Layer 2 (HMVC Engine & Cross-Module Isolation):** Memblokir eksekusi controller langsung atau bypass via HMVC jika modul belum diotorisasi.
- **Layer 3 (Core BaseController Guard):** Pengecekan lisensi pada inisialisasi constructor controller (`App\Http\Controllers\Core`).
- **Layer 4 (RSA-2048 Digital Licensing & Authority Passcode):** Tanda tangan digital asimetris dengan verifikasi hash satu arah (SHA-256 constant-time). Repositori klien **bebas dari hardcoded plaintext passcodes & private keys**.
- **Layer 5 (Integrity Verification & Anti-Tampering Engine):** Memeriksa integritas SHA-256 seluruh file core security. Modifikasi file ilegal langsung memicu *fail-closed*.
- **Layer 6 (AES-256-GCM Envelope Encryption & Centralized Master Manifest):**
  - **Fresh Clone State:** Source code controller & model tersimpan dalam format terenkripsi **`.php.enc`** di `app/Modules/{Module}/Encrypted/` dengan master manifest terpusat di `app/Services/Dapcode/modules-manifest.json` yang dipantau oleh Layer 5.
  - **Auto-Minified Envelopes:** Amplop enkripsi `.php.enc` langsung ter-minify otomatis dalam format 1-baris JSON (`JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE`) saat perintah `php artisan dapcode:pack` dijalankan.
  - **Auto-Unlock:** Saat diaktivasi dengan lisensi resmi, file didekripsi menjadi `.php` di disk lokal.
  - **Auto-Lock:** Saat lisensi dicabut (*Revoke*), file `.php` dihapus dari disk sehingga kembali ke status terenkripsi dan fail-closed.
  - **Git Leak-Proof:** File `.php` plaintext diabaikan oleh `.gitignore` sehingga **hanya file `.php.enc` dan `modules-manifest.json` yang di-push ke GitHub**.
  - **Core Security Code Protection & JIT In-Memory Execution:** 20 file kode proteksi inti AegisGuard (Services, Middleware, Commands, Core Controller, HMVC Dispatcher) dapat dienkripsi menjadi amplop `.php.enc`. Ketika dalam kondisi terenkripsi, file tetap dieksekusi secara normal melalui *In-Memory JIT (Just-In-Time) Decryption Engine* (`AegisguardLoader`) tanpa pernah menuliskan kode plaintext ke disk.

### 3. 📦 PHP Code Minifier Engine with Secure SourceMap Vault
Engine kompresi dan dekompresi performa tinggi untuk file PHP (AegisGuard Protection, HMVC Modules, Controllers, Models, Routes, dan Enkripsi .enc):
- **Zero Plaintext Leak (`.bak` Elimination):** Tidak lagi meninggalkan file backup `.bak` di direktori source code. Seluruh kode asli dikompresi (`gzdeflate` level 9), dienkripsi, dan diarsipkan secara aman di `storage/app/dapcode/.sourcemaps/*.dapmap` (terisolasi dan diabaikan oleh Git).
- **100% Byte-for-Byte SHA-256 Exact Restoration:** Menjamin restorasi kode asli saat proses `unminify` persis 100% identik tanpa perubahan spasi, baris baru, indentasi, atau format karakter.
- **PHP Code Minification:** Menggunakan engine tokenizer native PHP (`php_strip_whitespace`) untuk membersihkan komentar, tab, dan baris baru secara instan dengan pengelolaan temporary file yang aman pada platform Linux & Windows.
- **Full Module Discovery & AegisGuard .enc Integration:** Otomatis memindai dan me-minify seluruh file PHP pada modul HMVC (`app/Modules/*`), file proteksi AegisGuard, serta seluruh file amplop terenkripsi (`*.enc` / `*.php.enc`) yang secara arsitektural terintegrasi ke dalam grup target `aegisguard`. Minifikasi juga mencakup master manifest terpusat `modules-manifest.json`.

### 4. 🖥️ Developer Web Terminal & Authority Signer (`/dapcode/terminal`)
- **Interactive Artisan Console:** Menjalankan perintah Laravel Artisan secara visual dengan riwayat perintah (*keyboard history*), auto-scroll, dan output berwarna.
- **Quick Command Presets:** Tombol pintas 1-klik untuk status/minifikasi kode (`code:status all`, `code:minify all`, `code:unminify all`), status modul & pack (`dapcode:module`, `dapcode:pack all`), serta manajemen core AegisGuard (`aegisguard:status`, `aegisguard:encrypt`, `aegisguard:decrypt`).
- **Dedicated Modal Actions:**
  - **`+ Make Module`**: Membuat modul baru lengkap (Controller, Model, View, Core Base Controller, dan auto-enkripsi Layer 6).
  - **`🗑️ Remove Module`**: Menghapus modul secara aman beserta registrasinya.
  - **`⚡ Minify PHP Code`**: Menjalankan minifikasi kode PHP (Semua Modul, Controller, Model, AegisGuard, Routes) secara visual.
  - **`🔄 Unminify PHP Code`**: Merestorasi kode asli dari Secure SourceMap Vault secara visual.
- **RSA-2048 License Signer:** Menghasilkan signed activation payload dan signed revocation token secara instan dengan proteksi passcode Authority.
- **Custom Floating Toast Notifications:** Seluruh notifikasi menggunakan komponen UI modern (*no native browser alerts*).

### 5. ⚡ Modern Frontend Asset Pipeline (Laravel Vite)
- Ditenagai **Vite** & **laravel-vite-plugin** dengan kompilasi super cepat dan *Hot Module Replacement* (HMR).
- Integrasi Blade native melalui directive `@vite(['resources/css/app.css', 'resources/js/app.js'])`.
- Modul JavaScript modern berbasis standard ES Modules (ESM).

### 6. 🇮🇩 Indonesian Holiday & Celebration Theme Engine
Sistem tema dinamis yang otomatis mendeteksi kalender hari besar nasional Indonesia:
- **HUT Kemerdekaan RI (17 Agustus):** Merah Putih, font *Cinzel*, glow kemerdekaan.
- **Hari Raya Idul Fitri & Ramadhan:** Emerald & Gold, font *Amiri*, ornamen islami.
- **Tahun Baru Imlek:** Imperial Crimson & Gold, font *Playfair Display*.
- **Hari Raya Natal & Tahun Baru:** Pine Green & Crimson Snow.
- **Hari Lahir Pancasila, Sumpah Pemuda, Hari Pahlawan, Hari Kartini, Waisak, & Tahun Baru Masehi.**
- **Manual Selector:** Pengguna dapat mengganti tema secara bebas melalui ikon palet di header (`/theme/{key}`).

### 7. 🌐 Dual-Language Localization (ID / EN)
- Dukungan penuh multi-bahasa untuk seluruh modul dan antarmuka sistem (`/lang/id` & `/lang/en`).

### 8. 🎨 DapCode Internal UI Component Library & Design System
Sistem antarmuka terstandarisasi yang dirancang dengan estetika *modern dark mode*, konsistensi token CSS, dan modularitas tinggi untuk mempercepat pembangunan fitur tanpa membuat komponen dari nol:
- **22+ Komponen Blade Mandiri (`<x-ui.*>`):**
  - **Primitives:** Button (beragam varian tema, ukuran `sm/md/lg`, pill shape, loading spinner, dropdown actions), Badge (subtle, solid, outline, dot indicator), Status Indicator (pulsing radar dots), Alert (accent border, auto dismissible).
  - **Cards & Feedback:** Card (title, subtitle, icon, custom header/footer/action slots), Stat Card (trend counter indicator), Loading (animated spinner & shimmering skeleton), Empty State.
  - **Form Controls:** Input (icon, help text, floating labels), Select (custom styled dropdowns), DatePicker (modern native picker), Textarea, Switch Toggle, Radio Button, File Upload (drag & drop ready).
  - **Table & Navigation:** Table (striped, hoverable), DataTable (client-side dynamic fast search, custom action bar & pagination), Filter Bar, Breadcrumb Navigation Trail, Tabs (pills & underline styles).
  - **Interactive Dialogs & Popups:** Modal (`<x-ui.modal>` dengan pilihan ukuran `sm/md/lg/xl`, static backdrop, autofocus otomatis, Escape key), Floating Tooltips, Notification Toast (`DapToast`), Dialog Konfirmasi Promise-based (`DapConfirm`).
- **Interactive Playground & Code Guide (`/dapcode/ui-showcase`):**
  Halaman dokumentasi visual hidup di mana programmer dapat menguji coba setiap komponen secara langsung dan menyalin baris kodenya dengan satu klik (*one-click copy with toast feedback*).
- **HMVC Module Generator (`php artisan make:hmvc-module`):**
  Perintah otomatis untuk menghasilkan modul HMVC baru yang langsung mewarisi Base Controller dan terintegrasi penuh dengan ekosistem UI Component Library.

---

## 📂 13 Modul HMVC Terproteksi

| # | Modul | Kategori | Status Fresh Clone | Rute Utama |
|---|---|---|---|---|
| 1 | **Dashboard** | Core Analytics | 🔒 **LOCKED (Encrypted)** | `/dashboard` |
| 2 | **Profile** | Bio & Identitas | 🔒 **LOCKED (Encrypted)** | `/profile` |
| 3 | **Education** | Riwayat Akademik | 🔒 **LOCKED (Encrypted)** | `/education` |
| 4 | **Commerce** | Katalog Produk & Layanan | 🔒 **LOCKED (Encrypted)** | `/commerce` |
| 5 | **Research** | Riset & Publikasi Ilmiah | 🔒 **LOCKED (Encrypted)** | `/research` |
| 6 | **Career** | Rekam Jejak Karir | 🔒 **LOCKED (Encrypted)** | `/career` |
| 7 | **Activity** | Komunitas & Organisasi | 🔒 **LOCKED (Encrypted)** | `/activity` |
| 8 | **Media** | Galeri Multimedia | 🔒 **LOCKED (Encrypted)** | `/media` |
| 9 | **Achievement** | Prestasi & Penghargaan | 🔒 **LOCKED (Encrypted)** | `/achievement` |
| 10 | **Certification** | Sertifikasi & Lisensi Profesi | 🔒 **LOCKED (Encrypted)** | `/certification` |
| 11 | **Interest** | Bidang Minat & Keahlian | 🔒 **LOCKED (Encrypted)** | `/interest` |
| 12 | **Project** | Portofolio Proyek | 🔒 **LOCKED (Encrypted)** | `/project` |
| 13 | **Setting** | Konfigurasi Sistem | 🔒 **LOCKED (Encrypted)** | `/setting` |

---

## 🛠️ Struktur Direktori Proyek

```text
dapcode-app/
├── app/
│   ├── Console/Commands/
│   │   ├── CreateDatabaseCommand.php  # Auto Create Database: php artisan db:create
│   │   ├── CodeMinifyCommand.php      # Minify PHP & Blade: php artisan code:minify
│   │   ├── CodeUnminifyCommand.php    # Unminify PHP & Blade: php artisan code:unminify
│   │   ├── CodeStatusCommand.php      # Status Minifikasi: php artisan code:status
│   │   ├── DapcodeAegisguardCommand.php # Status proteksi AegisGuard: php artisan aegisguard:status (alias: dapcode:aegisguard)
│   │   ├── DapcodeAegisguardEncryptCommand.php # Enkripsi core & auto-minify: php artisan aegisguard:encrypt (alias: dapcode:aegisguard-encrypt)
│   │   ├── DapcodeAegisguardDecryptCommand.php # Dekripsi core & auto-unminify: php artisan aegisguard:decrypt (alias: dapcode:aegisguard-decrypt)
│   │   ├── ViewMinifyCommand.php      # Minify Views: php artisan view:minify
│   │   ├── ViewUnminifyCommand.php    # Unminify Views: php artisan view:unminify
│   │   ├── DapcodeModuleCommand.php   # Status modul: php artisan dapcode:module
│   │   ├── DapcodePackCommand.php     # Re-encrypt kode develop: php artisan dapcode:pack
│   │   ├── MakeHMVCModule.php         # Generator modul baru & UI: php artisan make:hmvc-module
│   │   ├── RemoveHMVCModule.php       # Hapus modul HMVC: php artisan remove:module
│   │   └── SignDapcodeLicense.php     # Authority CLI Signer (RSA-2048 Private Key)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php         # Base Controller (Layer 3 Guard)
│   │   │   ├── PortfolioController.php # Landing Page Portofolio (/)
│   │   │   ├── Dapcode/
│   │   │   │   └── LicenseController.php # Activation, Terminal & Signer Handlers
│   │   │   ├── Dev/
│   │   │   │   └── UiShowcaseController.php # UI Component Library Showcase Controller
│   │   │   └── Core/                  # Base Controllers per Modul
│   │   └── Middleware/
│   │       └── DapcodeLicenseMiddleware.php # Layer 1 Dynamic Route Interceptor
│   ├── Modules/                       # 13 Modul HMVC Terenkripsi
│   │   ├── Dashboard/
│   │   │   ├── Encrypted/             # File .php.enc (Naik ke Git)
│   │   │   ├── Controllers/           # Plaintext .php (Lokal saat aktif, di-.gitignore)
│   │   │   ├── Models/                # Plaintext .php (Lokal saat aktif, di-.gitignore)
│   │   │   └── Views/                 # Blade View Templates (Naik ke Git)
│   │   └── ... (13 Modules)
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   └── HMVCServiceProvider.php    # Dynamic HMVC Route & View Namespace Loader
│   └── Services/
│       ├── Dapcode/                   # DapCode AegisGuard™ Core Engine
│       │   ├── modules-manifest.json  # Master Centralized Module Manifest (Naik ke Git)
│       │   ├── AegisguardLoader.php   # JIT In-Memory AES-256-GCM Envelope Execution Engine
│       │   ├── InstallationService.php # ID Instalasi Unik Persisten (DAP-XXXXXX-...)
│       │   ├── LicenseVerifier.php    # Verifikasi Kriptografi RSA-2048 & Hash Passcode
│       │   ├── ActivationService.php  # Handler Aktivasi & Pencabutan Lisensi
│       │   ├── ModuleEncryptionService.php # AES-256-GCM Envelope Encryption Engine
│       │   ├── LicenseGuard.php       # Sentral Pengecekan Izin Akses Modul
│       │   ├── IntegrityService.php   # Layer 5 SHA-256 Anti-Tampering Engine
│       │   ├── SourceMapVaultService.php # Encrypted SourceMap Vault Engine
│       │   ├── CodeMinifierService.php # PHP & Blade Code Minifier Engine
│       │   └── CodeFormatterService.php # Code Unminifier & Fallback Beautifier
│       ├── View/
│       │   ├── ViewMinifierService.php  # Blade Views Minification Engine
│       │   └── ViewFormatterService.php # Blade Views Formatter & Beautifier
│       └── HMVC/
│           └── HMVC.php               # Core Dispatcher & Hierarchical Request Engine
├── config/
│   └── dapcode.php                    # Konfigurasi Modul & Kriptografi
├── docs/
│   └── security/
│       ├── dapcode-threat-model.md    # Threat Model & Trust Boundaries
│       └── dapcode-license-architecture.md # Architecture & Hardening Guide
├── resources/
│   ├── css/
│   │   ├── app.css                    # Entry Point CSS
│   │   └── components/                # 10 CSS Design Tokens & Component Stylesheets
│   ├── js/
│   │   ├── app.js                    # Entry Point JS
│   │   └── components/                # 7 Micro-Driver JS (Modal, Toast, Confirm, Tabs, dll.)
│   └── views/
│       ├── components/ui/             # 22+ Blade UI Design System Components (<x-ui.*>)
│       ├── showcase/
│       │   └── index.blade.php        # Interactive UI Showcase Playground View
│       ├── dapcode/
│       │   ├── activate.blade.php     # Form Aktivasi & Deaktivasi Lisensi
│       │   ├── authority-terminal.blade.php # Developer Web Terminal & RSA-2048 Signer
│       │   └── license-required.blade.php # Tampilan Error Saat Modul Terkunci (403)
│       └── portfolio.blade.php        # Landing Page Portofolio
├── storage/
│   └── app/
│       └── dapcode/
│           └── .sourcemaps/           # Secure SourceMap Vault (*.dapmap, di-.gitignore)
├── tests/
│   └── Feature/
│       ├── DapcodeEncryptedModuleSecurityTest.php # 34 Tests Enkripsi AES-256-GCM
│       ├── DapcodeLayeredGuardSecurityTest.php    # 12 Tests Multi-Layer Protection
│       ├── DapcodeLicenseSecurityTest.php         # 22 Tests Verifikasi Lisensi RSA
│       └── UiComponentLibraryTest.php             # 11 Tests UI Component Library
└── routes/
    └── web.php                        # Routing Web, Showcase & DapCode Endpoints
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

### 3. Konfigurasi Lingkungan, Database & Migrasi

Anda dapat memilih **1-Langkah Otomatis** atau langkah manual:

#### ⚡ Opsi A: 1-Langkah Otomatis (Direkomendasikan)
```bash
composer setup
```
> Perintah `composer setup` akan otomatis membuat `.env` dari `.env.example`, meng-generate `APP_KEY`, membuat database fisik (`dapcode_app`) di MySQL melalui `php artisan db:create`, dan mengeksekusi seluruh migrasi tabel via `php artisan migrate --force`.

#### 🛠️ Opsi B: Langkah Manual
```bash
# 1. Salin file environment
cp .env.example .env

# 2. Generate Application Key
php artisan key:generate

# 3. Buat Database Otomatis (MySQL/PostgreSQL/SQLite)
php artisan db:create

# 4. Eksekusi Migrasi Database
php artisan migrate
```

### 4. Build Frontend Asset (Vite)
```bash
# Mode Development (Live Hot-Reload):
npm run dev

# Atau Mode Production (Build & Minify):
npm run build
```

### 5. Menjalankan Server Laravel
```bash
php artisan serve
```
Buka browser pada: **`http://127.0.0.1:8000`**

---

## ⚡ Panduan Minify & Unminify (PHP Code & HMVC Modules)

Anda dapat mengompresi dan merestorasi kode PHP melalui **Web Terminal** di `http://127.0.0.1:8000/dapcode/terminal` (menu *Quick Command Presets*) atau melalui Artisan CLI:

### 1. Minifikasi Kode PHP (`code:minify`)
```bash
# Minify seluruh file PHP (Semua Modul, Controller, Model, AegisGuard, Routes) sekaligus:
php artisan code:minify all

# Minify seluruh file PHP pada semua modul HMVC (app/Modules/*):
php artisan code:minify modules

# Minify seluruh Controllers yang tidak dienkripsi (app/Http/Controllers & Modules):
php artisan code:minify controllers

# Minify seluruh Models yang tidak dienkripsi (app/Models & Modules):
php artisan code:minify models

# Minify seluruh file proteksi DapCode AegisGuard (Services, Master Manifest, Core, Middleware, Commands, dan File Enkripsi .enc):
php artisan code:minify aegisguard

# Inspeksi status seluruh file aegisguard & amplop enkripsi .enc:
php artisan code:status aegisguard

# Minify kelompok seluruh file routes, middlewares, helpers, Http/Kernel.php, dan portofolio:
php artisan code:minify routes

# Minify 1 modul spesifik (misal: Blog):
php artisan code:minify Blog
```

### 2. Restorasi Kode Asli (`code:unminify`)
```bash
# Restorasi seluruh file dari Secure SourceMap Vault:
php artisan code:unminify all

# Restorasi seluruh modul HMVC ke 100% kode asli:
php artisan code:unminify modules

# Restorasi Controllers ke 100% kode asli:
php artisan code:unminify controllers

# Restorasi Models ke 100% kode asli:
php artisan code:unminify models

# Restorasi file proteksi AegisGuard dan .enc ke 100% kode asli:
php artisan code:unminify aegisguard

# Restorasi kelompok seluruh file routes, middlewares, helpers, Http/Kernel.php, dan portofolio:
php artisan code:unminify routes

# Restorasi 1 modul spesifik:
php artisan code:unminify Blog
```

---

## 💻 Panduan Pengembangan Modul Baru (*Developer Workflow*)

### A. Membuat Modul Baru:
Gunakan Web Terminal di `http://127.0.0.1:8000/dapcode/terminal` lalu klik tombol **`+ Make Module`**, atau via CLI:
```bash
php artisan make:module Blog
```
*Perintah ini otomatis membuat struktur Controller, Model, View, Core Base Controller, mengemas enkripsi `.php.enc`, dan mengunci modul dalam status fresh clone.*

### B. Mengembangkan & Mengemas Kode Terbaru (*Pack*):
Setelah Anda mengedit kode `.php` modul lokal dan ingin merilisnya ke Git/GitHub:
```bash
# Mengemas 1 modul spesifik:
php artisan dapcode:pack Blog

# Atau mengemas seluruh modul sekaligus:
php artisan dapcode:pack all

# Mengemas dengan memaksa penguncian (purge plaintext) meskipun lisensi lokal aktif:
php artisan dapcode:pack all --lock
```
*Fitur Unggulan `dapcode:pack`:*
1. **Otomatis Ter-Minify dari Awal:** File `.php.enc` yang dihasilkan dan master manifest terpusat (`app/Services/Dapcode/modules-manifest.json`) langsung dikompresi ke dalam format 1-baris JSON (`JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE`) tanpa `JSON_PRETTY_PRINT` atau indentasi berlebih, menjamin integritas dan status minifikasi permanen.
2. **License-Aware Development Mode:** Jika mesin lokal memiliki lisensi aktif untuk modul tersebut, file plaintext tetap berstatus `UNLOCKED` sehingga pengembangan dan akses rute tetap berjalan lancar.
3. **Summary Transparan:** Menampilkan daftar nama modul yang berhasil dikemas secara rapi (`Modul dikemas: dashboard, ...`).
4. **Git Safe:** Manifest integritas otomatis diperbarui dan file `.php` lokal dicegah bocor ke Git oleh `.gitignore`.

### C. Menghapus Modul:
```bash
php artisan remove:module Blog
```

---

## 🛡️ Panduan Proteksi Core AegisGuard (Enkripsi & Dekripsi Core Code)

Selain modul HMVC, DapCode AegisGuard™ dilengkapi kemampuan **Enkripsi Amplop Biner AES-256-GCM** untuk **20 file kode inti proteksi arsitektur** (Services, Middleware, Commands, Core Controller, dan HMVC Engine).

### ⚙️ Mekanisme In-Memory JIT Decryption (`AegisguardLoader`)
Ketika seluruh file core AegisGuard dalam status terenkripsi (`.php.enc`), aplikasi **tetap dapat berjalan 100% normal**:
* `AegisguardLoader` secara dinamis mendekripsi amplop biner secara Just-In-Time (JIT) langsung di dalam memori PHP (`eval()`).
* **Zero Plaintext Leak:** Tidak pernah menuliskan source code plaintext ke disk penyimpanan saat mode terenkripsi aktif.

### 🔄 Alur Pengembangan Core AegisGuard (*Workflow Modifikasi Kode Core*):
Jika Anda perlu mengubah, mengembangkan, atau melakukan audit pada kode inti proteksi AegisGuard:

1. **Dekripsi Kode Core ke Format Asli & Auto-Unminify Seluruh Proyek:**
   ```bash
   # Melalui CLI (Perintah Utama atau Alias):
   php artisan aegisguard:decrypt
   # Atau: php artisan dapcode:aegisguard decrypt
   # Atau klik tombol preset di Web Terminal: aegisguard:decrypt
   ```
   *Seluruh 20 file kode core AegisGuard didekripsi dari `.enc` ke kode sumber asli, dan sistem secara otomatis memanggil `code:unminify all --force`. Kode dipulihkan dari SourceMap Vault ke format multiline yang terstruktur rapi sesuai standar PSR-12.*

2. **Lakukan Modifikasi Kode:**
   Edit file `.php` pada `app/Services/Dapcode/`, `app/Console/Commands/`, `app/Http/Middleware/`, dll. sesuai kebutuhan pengembangan Anda.

3. **Enkripsi Kembali ke Amplop Biner & Auto-Minify Seluruh Proyek:**
   Setelah pengeditan selesai, segel kembali ke status produksi terenkripsi:
   ```bash
   # Melalui CLI (Perintah Utama atau Alias):
   php artisan aegisguard:encrypt
   # Atau: php artisan dapcode:aegisguard encrypt
   # Atau klik tombol preset di Web Terminal: aegisguard:encrypt
   ```
   *Perintah ini mengenkripsi seluruh file core menjadi amplop `.php.enc` ter-minify, mengganti file `.php` asli dengan safe loader stub minified 2 baris, dan secara otomatis mengeksekusi `code:minify all --force` sehingga 100% kode proyek ter-minify optimal.*

4. **Inspeksi Status Proteksi Core:**
   ```bash
   # Melalui CLI:
   php artisan aegisguard:status
   # Atau: php artisan dapcode:aegisguard status
   # Atau klik tombol preset di Web Terminal: aegisguard:status
   ```
   *Menampilkan status ketersediaan kode `.php`, amplop `.php.enc`, dan kesiapan in-memory JIT execution.*

---

## 🔑 Panduan Otorisasi Lisensi (Authority Signer)

Sebagai pemilik (*Owner / Authority*), Anda dapat men-generate payload lisensi bertanda tangan digital (**RSA-2048**) melalui:

### 1. 🖥️ Menggunakan Authority Web Terminal
Buka URL: **`http://127.0.0.1:8000/dapcode/terminal`**
1. Pilih Tab: **RSA-2048 License Signer**.
2. Pilih Aksi: **Otorisasi Lisensi (ACTIVATE)** atau **Pencabutan Lisensi (REVOKE)**.
3. Masukkan **Authority Passcode**.
4. Masukkan **Installation ID** klien (tersedia tombol *"Gunakan ID Instalasi Ini"*).
5. Pilih durasi dan modul yang diizinkan (atau *Full Wildcard `*`*).
6. Klik **"Tanda Tangani Digital"** &rarr; Salin payload JSON atau klik **"Terapkan ke Form Aktivasi Sistem"** untuk aktivasi instan 1-klik!

### 2. ⚡ Menggunakan Artisan CLI
```bash
# Generate Lisensi Penuh (Semua Modul *, Berlaku 2 Tahun):
php artisan dapcode:sign-license <INSTALLATION_ID> --passcode="<AUTHORITY_PASSCODE>"

# Generate Lisensi Parsial (Hanya Modul Tertentu):
php artisan dapcode:sign-license <INSTALLATION_ID> --modules=commerce --modules=project --passcode="<AUTHORITY_PASSCODE>"

# Generate Signed Revocation Token (Pencabutan Lisensi):
php artisan dapcode:sign-license --revoke --license_id=<LICENSE_ID> --passcode="<AUTHORITY_PASSCODE>"
```

---

## 🧪 Menjalankan Automated Security & UI Tests (100% Pass)

Aplikasi dilengkapi **82 Automated Feature, Security, & UI Tests** untuk menguji seluruh lapisan pertahanan dan integritas komponen antarmuka:

```bash
php artisan test
```

### Rincian Cakupan Test Suite:
* ✅ **`UiComponentLibraryTest` (12 Tests, 38 Assertions):** Menguji responsivitas halaman `/dapcode/ui-showcase`, render seluruh komponen tombol, badge, alert, card, form controls, modal, tabs, datatable auto-refresh, integrasi modul Setting, validasi asset manifest Vite, halaman landing portfolio, serta integrasi halaman aktivasi dan terminal.
* ✅ **`DapcodeEncryptedModuleSecurityTest` (34 Tests):** Menguji status fresh clone locked, dekripsi saat aktivasi, pembersihan plaintext saat revocation, cipher tampering, tag verification, path traversal, manipulasi manifest, siklus minify/unminify dengan lisensi aktif, pengelompokan file `.enc` ke target `aegisguard`, dan isolasi route portofolio.
* ✅ **`DapcodeLayeredGuardSecurityTest` (12 Tests):** Menguji ketahanan Layer 1–6 terhadap middleware bypass, controller direct invocation, HMVC sub-request injection, tampered core files, dan canonical path obfuscation.
* ✅ **`DapcodeLicenseSecurityTest` (22 Tests):** Menguji validasi tanda tangan RSA-2048, verifikasi expired date, granular module licensing, anti-forgery request headers/cookies, dan hash passcode validation.
* ✅ **`ExampleTest` (2 Tests):** Unit & basic application assertions.

---

## 🎨 Penyesuaian Tema Hari Raya (Theme Switcher)

Anda dapat menguji tema perayaan secara langsung melalui URL:
* **Otomatis (Berdasarkan Kalender):** `http://127.0.0.1:8000/theme/auto`
* **HUT Kemerdekaan RI:** `http://127.0.0.1:8000/theme/kemerdekaan`
* **Hari Raya Idul Fitri:** `http://127.0.0.1:8000/theme/idulfitri`
* **Tahun Baru Imlek:** `http://127.0.0.1:8000/theme/imlek`
* **Hari Raya Natal & Tahun Baru:** `http://127.0.0.1:8000/theme/natal`
* **Hari Lahir Pancasila:** `http://127.0.0.1:8000/theme/pancasila`
* **Hari Sumpah Pemuda:** `http://127.0.0.1:8000/theme/pemuda`
* **Hari Pahlawan:** `http://127.0.0.1:8000/theme/pahlawan`
* **Hari Kartini:** `http://127.0.0.1:8000/theme/kartini`
* **Hari Raya Waisak:** `http://127.0.0.1:8000/theme/waisak`
* **Tahun Baru Masehi:** `http://127.0.0.1:8000/theme/tahunbaru`

---

## 📄 Lisensi (License)

Aplikasi ini bersifat *open-source* di bawah lisensi [MIT License](LICENSE).  
Dikembangkan dengan ❤️ oleh **[DapCode Studio](https://github.com/codedaffa)**.
