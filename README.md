# 🚀 DapCode App — Modular HMVC Portfolio & Developer Ecosystem

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel Framework">
  <img src="https://img.shields.io/badge/PHP-7.4%2B%20%7C%208.x-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Architecture-HMVC%20Modular-6366f1?style=for-the-badge" alt="HMVC">
  <img src="https://img.shields.io/badge/Theme%20Engine-Indonesian%20Holidays-dc2626?style=for-the-badge" alt="Indonesian Holidays">
  <img src="https://img.shields.io/badge/Localization-ID%20%7C%20EN-38bdf8?style=for-the-badge" alt="i18n">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License">
</p>

**DapCode App** adalah platform portofolio digital dan aplikasi ekosistem pengembang modern yang dibangun menggunakan framework **Laravel** dengan arsitektur modular **HMVC (Hierarchical Model-View-Controller)**. Platform ini dilengkapi sistem tema perayaan nasional Indonesia otomatis, lokalisasi dwi-bahasa (ID/EN), antarmuka *glassmorphism* modern, dan 13 modul fungsional terintegrasi.

---

## 🌟 Fitur Utama (Key Features)

### 1. 🏛️ Arsitektur Modular HMVC
Seluruh fitur dikelompokkan dalam modul independen di dalam direktori `app/Modules/`:
- **Model, View, Controller, dan Route** tersendiri untuk setiap modul.
- *Service Provider* dinamis (`HMVCServiceProvider`) yang mendaftarkan view namespace, helper, dan route otomatis.

### 2. 🇮🇩 Indonesian Holiday & Celebration Theme Engine
Sistem tema dinamis yang otomatis mendeteksi kalender hari besar nasional Indonesia dan mengubah tampilan secara real-time:
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

### 3. 🌐 Dual-Language Localization (ID / EN)
- Dukungan penuh multi-bahasa untuk seluruh modul dan elemen sistem.
- Beralih bahasa secara instan melalui switcher di header (`/lang/id` & `/lang/en`).

### 4. 🧭 Navigasi Interaktif & Header Title Module Switcher
- **Header Title Dropdown:** Klik pada judul header (`<h1 class="header-title">`) untuk membuka direktori 13 modul HMVC secara instan.
- **Contextual Sidebar:** Sidebar fokus khusus menampilkan sub-menu modul yang sedang aktif tanpa elemen yang menumpuk.
- **No-Scrollbar Design:** Tampilan rapi bebas scrollbar yang mengganggu.

---

## 📂 13 Modul HMVC Terintegrasi

| # | Modul | Deskripsi | Rute |
|---|---|---|---|
| 1 | **Dashboard** | Statistik analitik, metrik performa & ringkasan sistem | `/dashboard` |
| 2 | **Profile** | Biodata pribadi, keahlian teknis & kontak | `/profile` |
| 3 | **Education** | Riwayat akademik, kursus & sertifikasi pelatihan | `/education` |
| 4 | **Certification** | Daftar lisensi profesional & verifikasi kompetensi | `/certification` |
| 5 | **Achievement** | Penghargaan kejuaraan & kompetisi | `/achievement` |
| 6 | **Interest** | Bidang riset, minat teknologi & hobi | `/interest` |
| 7 | **Project** | Katalog portofolio proyek web, mobile & open-source | `/project` |
| 8 | **Research** | Publikasi ilmiah, jurnal & eksperimen laboratorium | `/research` |
| 9 | **Career** | Rekam jejak pengalaman kerja & proyek freelance | `/career` |
| 10 | **Activity** | Kegiatan organisasi, komunitas & workshop | `/activity` |
| 11 | **Media** | Galeri berkas multimedia, foto, video & dokumen | `/media` |
| 12 | **Commerce** | Katalog produk digital, layanan jasa & pesanan | `/commerce` |
| 13 | **Setting** | Konfigurasi sistem, tema perayaan & preferensi bahasa | `/setting` |

---

## 🛠️ Struktur Direktori Proyek

```text
dapcode-app/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Core/                  # Base Controllers untuk HMVC
│   ├── Modules/                       # Modul-Modul HMVC
│   │   ├── Dashboard/
│   │   ├── Profile/
│   │   ├── Project/
│   │   ├── Setting/
│   │   └── ... (13 Modules)
│   ├── Providers/
│   │   └── HMVCServiceProvider.php    # Dynamic HMVC Route & View Loader
│   └── Services/
│       └── Theme/
│           └── HolidayThemeService.php # Indonesian Celebration Engine
├── public/
│   └── assets/
│       ├── css/
│       │   ├── holiday-themes.css     # CSS 10 Tema Hari Raya
│       │   └── theme-responsive.css   # Core Design System & Responsive Layout
│       └── js/
│           └── theme-responsive.js    # Sidebar, Modal & Dropdown Controller
├── resources/
│   ├── lang/
│   │   ├── id/                        # Bahasa Indonesia Translation
│   │   └── en/                        # English Translation
│   └── views/
│       ├── portfolio.blade.php        # Landing Page Portofolio (/)
│       └── theme/                     # Master Layout, Header, Sidebar, Footer
└── routes/
    └── web.php                        # Root Web Routes
```

---

## 🚀 Instalasi & Menjalankan Aplikasi

### 1. Kloning Repositori
```bash
git clone https://github.com/codedaffa/dapcode-app.git
cd dapcode-app
```

### 2. Instal Dependensi Composer
```bash
composer install
```

### 3. Konfigurasi Lingkungan (.env)
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Menjalankan Server Lokal
```bash
php artisan serve
```
Akses aplikasi melalui browser: **`http://127.0.0.1:8000`**

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
- **Hari Kartini:** `http://127.0.0.1:8000/theme/kartini`
- **Hari Raya Waisak:** `http://127.0.0.1:8000/theme/waisak`
- **Tahun Baru Masehi:** `http://127.0.0.1:8000/theme/tahunbaru`

---

## 📄 Lisensi (License)

Aplikasi ini bersifat *open-source* di bawah lisensi [MIT License](LICENSE).
Dikembangkan dengan ❤️ oleh **[DapCode Studio](https://github.com/codedaffa)**.
