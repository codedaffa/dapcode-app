# ⚡ DAPCODE CODE MINIFIER & UNMINIFIER — MASTER IMPLEMENTATION PROMPT TEMPLATE

> **Panduan Penggunaan:** Salin seluruh isi template prompt di dalam blok kode di bawah ini dan tempelkan (*copy-paste*) ke AI Coding Assistant (Antigravity, Claude, ChatGPT, Cursor, dll.) pada proyek Laravel baru Anda untuk mengimplementasikan sistem **Code Minifier, Unminify (SourceMap Vault), & Code Status Inspector** secara otomatis, teruji, dan siap produksi.

---

```markdown
# ⚡ SYSTEM PROMPT: IMPLEMENTASI SISTEM CODE MINIFIER, UNMINIFIER & SOURCEMAP VAULT

Anda bertindak sebagai **Principal Software Engineer & Laravel Architecture Specialist**.

Tugas Anda adalah mengimplementasikan sistem **Code Minifier & Unminifier Enterprise-Grade** (dilengkapi dengan **Secure SourceMap Vault**, **PHP Code Beautifier Fallback**, dan **Code Status Inspector**) secara lengkap ke dalam proyek Laravel ini.

---

## 🏛️ 1. ARSITEKTUR & PRINSIP UTAMA SISTEM

1. **Kompresi Multi-Bahasa Tanpa Dependencies Eksternal:**
   - **PHP Code:** Menggunakan `php_strip_whitespace()` native PHP yang cepat dan aman.
   - **Blade Templates:** Mengompres HTML, tag `<style>` (CSS), tag `<script>` (JS), dan blok `@php ... @endphp` menjadi **1 baris utuh**, dengan tetap melindungi elemen yang sensitif terhadap whitespace seperti `<pre>`, `<textarea>`, dan `<code>`.
   - **JSON / Manifest:** Menggunakan `json_decode()` + `json_encode()` tanpa indentasi (`JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE`).

2. **Secure SourceMap Vault (Zero Data-Loss Roundtrip):**
   - Sebelum file diminify, file asli wajib di-backup secara terisolasi di dalam vault (`storage/framework/dapcode/vault/` atau path aman lainnya) yang diproteksi `.gitignore`.
   - Menggunakan hashing SHA-256 untuk memvalidasi integritas file asli saat di-restore.
   - Saat menjalankan `code:unminify`, sistem memprioritaskan pemulihan **100% kode asli byte-for-byte** dari Vault.

3. **PHP Code Beautifier Fallback:**
   - Jika file tidak memiliki rekaman di SourceMap Vault (misal file baru atau vault terhapus), sistem memiliki fallback tokenizer PHP native (`token_get_all`) untuk memformat ulang kode secara rapi sesuai standar PSR-12 (indentasi 4 spasi atau 2 spasi).
   - **PENTING:** Proses unminify/beautifier **DILARANG** menambahkan baris kosong baru berlebih di akhir file (`rtrim($code) . "\n"`).

4. **Code Status Inspector Heuristic:**
   - Command `code:status` harus akurat mendeteksi apakah file berstatus `⚡ MINIFIED` atau `📄 ORIGINAL` berdasarkan jumlah baris (`$lines <= 3`) dan rasio ukuran terhadap panjang baris (`$avgLineLen`), bukan sekadar ekstensi file.

5. **Aturan Penting untuk Mencapai 100% Minifikasi (Pencegahan File Tertinggal):**
   - **PHP Heredocs:** `php_strip_whitespace()` secara spesifikasi PHP **TIDAK** menghilangkan baris baru di dalam heredoc (`<<<EOT`, `<<<PHP`, `<<<BLADE`). Gunakan string standar berpetik ganda dengan escape `\n` untuk konstanta kunci (misal RSA PEM) atau template stubs agar file dapat terkompresi menjadi 1–2 baris.
   - **Blade `<style>` & `<script>`:** Jangan memproteksi `<style>` dan `<script>` sebagai raw block. Hapus komentar (`/* ... */` dan `// ...`) dan padatkan whitespace pada CSS dan JS.
   - **Textarea Placeholders:** Pastikan atribut `placeholder` di dalam tag `<textarea>` menggunakan format string satu baris (single-line JSON).

---

## 📁 2. FILE DAN KOMPONEN YANG HARUS DIBUAT

### A. Services (`app/Services/Dapcode/` atau namespace kustom)

#### 1. `SourceMapVaultService.php`
- **Fungsi:** Mengelola penyimpanan backup file asli sebelum minifikasi.
- **Lokasi Vault:** `storage/framework/dapcode/vault/` (otomatis dibuat jika belum ada).
- **Struktur Metadata:**
  ```json
  {
    "file": "app/Models/User.php",
    "hash_original": "sha256...",
    "hash_minified": "sha256...",
    "minified_at": "2026-09-06T12:00:00Z",
    "original_content": "...isi file asli..."
  }
  ```
- **Method yang wajib ada:**
  - `storeMap(string $filePath, string $originalContent, string $minifiedContent): bool`
  - `restoreOriginal(string $filePath): ?string`
  - `hasMap(string $filePath): bool`
  - `cleanVault(): int`

#### 2. `CodeFormatterService.php` (PHP Beautifier Native)
- **Fungsi:** Memformat kode PHP yang minified kembali menjadi kode berstruktur rapi jika SourceMap Vault tidak tersedia.
- **Implementasi:** Menggunakan PHP Lexer `token_get_all()` tanpa library composer luar.
- **Fitur:**
  - Indentasi terkonfigurasi (default 4 spasi).
  - Penataan kurung kurawal `{` dan `}` dengan newline yang rapi.
  - Penataan blok `class`, `function`, `if`, `foreach`, `switch`, `try-catch`.
  - Normalisasi baris akhir: `rtrim($code) . "\n"` (tepat 1 newline penutup, tidak ada baris kosong ganda).

#### 3. `CodeMinifierService.php`
- **Fungsi:** Engine utama minifikasi dan analisis status kode.
- **Target File Discovery:**
  - Mendukung target `'all'`, `'aegisguard'` (murni file proteksi security engine, licensing, services, middleware, commands, master manifest JSON terpusat, serta seluruh file amplop enkripsi `.enc` / `*.php.enc`), `'modules'`, `'routes'` (seluruh file routes, middlewares, helpers, `app/Http/Kernel.php` & halaman utama portofolio), `'middlewares'`, `'helpers'`, `'kernel'`, `'views'`, atau path file/direktori spesifik.
  - **Prinsip Pengelompokan Portofolio, Middleware, Kernel & Helper:** File halaman utama portofolio (`PortfolioController.php` dan `resources/views/portfolio.blade.php`), seluruh file helper (`app/Helpers/*.php`, `ViteHelper.php`), seluruh file middleware (`app/Http/Middleware/*.php`), dan `app/Http/Kernel.php` secara arsitektur digabungkan ke dalam kelompok target `'routes'` (seluruh file route & halaman utama portofolio) dan `'all'` (semua sistem).
  - **Prinsip Pengelompokan File Enkripsi `.enc` & Amplop AegisGuard:** Seluruh file amplop modul terenkripsi (`app/Modules/*/Encrypted/*.enc` dan `*.php.enc`) serta seluruh file kode core AegisGuard terenkripsi (`*.php.enc`) secara arsitektural digabungkan ke dalam target `'aegisguard'` (dan `'all'`).
  - **Isolasi Vault untuk `.enc` & `modules-manifest.json`:** File `.enc` dan `modules-manifest.json` adalah artefak status dinamis kriptografis mesin. File-file ini **DILARANG** disimpan di SourceMap Vault atau di-restore dari snapshot vault lama. Saat di-minify, JSON dipadatkan; saat di-unminify, file cukup di-beautify (`JSON_PRETTY_PRINT`) dari konten aktifnya saat ini tanpa mengubah ciphertext, IV, tag, dan salt.
  - **Otomatisasi Minifikasi pada Packaging:** Saat perintah `dapcode:pack` atau `dapcode:aegisguard encrypt` dijalankan, amplop enkripsi `.enc` langsung dikemas dalam format minified (1-baris JSON).
- **Method yang wajib ada:**
  - `minifyPhp(string $content, string $filePath = ''): string`
    - Memanfaatkan `php_strip_whitespace()`.
    - **Safe Cross-Platform Temporary File:** Menggunakan `storage_path('framework/cache')` dengan `@tempnam` dan blok `try ... finally` pembersihan file untuk mencegah `E_NOTICE: tempnam(): file created in the system's temporary directory` yang dapat memicu `ErrorException` pada lingkungan Windows/PHP.
  - `minifyJson(string $content): string`
    - Memadatkan JSON string (`JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE`). Termasuk menangani file amplop `.enc`.
  - `minifyBlade(string $content): string`
    - Memadatkan blok `<style>` (hapus `/*...*/`, padatkan whitespace dan karakter `[:;,{}]`).
    - Memadatkan blok `<script>` (hapus komentar `//` dan `/*...*/`, satukan baris kode JS).
    - Melindungi tag `<pre>`, `<textarea>`, `<code>` dengan token placeholder unik (`___DAP_PROTECTED_BLOCK_X___`).
    - Memadatkan blok `@php ... @endphp`.
    - Menghapus komentar Blade `{{-- ... --}}` dan komentar HTML `<!-- ... -->`.
    - Merapatkan tag HTML (`> <` -> `><`).
    - Mengembalikan token placeholder ke bentuk aslinya.
  - `minifyFile(string $filePath, bool $createBackup = true): array`
  - `getFileStatus(string $filePath): ?array`
    - Mengembalikan detail file: `is_minified`, `lines`, `size`, `type`, `has_vault`.

---

### B. Artisan Commands (`app/Console/Commands/`)

#### 1. `CodeMinifyCommand.php`
- **Signature:** `code:minify {target=all : Target file atau grup (all, modules, routes, views, atau path)} {--force : Jalankan tanpa konfirmasi} {--no-vault : Jangan simpan backup di SourceMap Vault}`
- **Fungsi:**
  - Memindai file target.
  - Menampilkan progress bar interaktif.
  - Menyimpan file asli ke `SourceMapVaultService` sebelum overwrite.
  - Menampilkan tabel rangkuman: Target File, Tipe, Ukuran Asli, Ukuran Minified, Persentase Penghematan.

#### 2. `CodeUnminifyCommand.php`
- **Signature:** `code:unminify {target=all : Target file atau grup} {--force : Jalankan tanpa konfirmasi} {--format : Paksa format ulang (beautifier) abaikan vault} {--indent=4 : Jumlah spasi indentasi} {--clean : Hapus backup vault setelah restore}`
- **Fungsi:**
  - Memulihkan file minified ke kode asli (prioritas SourceMap Vault).
  - Jika vault tidak ada atau diberi flag `--format`, gunakan `CodeFormatterService`.
  - Menampilkan tabel hasil pemulihan: Target File, Tipe, Status Pemulihan (Restored Vault / Beautified), Ukuran Baru.

#### 3. `CodeStatusCommand.php`
- **Signature:** `code:status {target=all : Target file atau grup}`
- **Fungsi:**
  - Menganalisis kondisi file saat ini tanpa mengubah file.
  - Menampilkan tabel: File Path, Tipe (PHP/Blade/JSON), Status (`⚡ MINIFIED` / `📄 ORIGINAL`), Jumlah Baris, Ukuran File, Status Vault.
  - Menampilkan ringkasan statistik (Persentase file minified vs original, total ukuran).

---

## 💻 3. SPESIFIKASI KODE INTI

### Implementasi `CodeMinifierService::minifyBlade`
```php
public function minifyBlade(string $content): string
{
    if (empty($content)) {
        return '';
    }

    $placeholders = [];

    // 1. Minify tag <style> (CSS)
    $content = preg_replace_callback('/<style\b[^>]*>([\s\S]*?)<\/style>/i', function($m) {
        $attrs = '';
        if (preg_match('/<style\b([^>]*)>/i', $m[0], $am)) {
            $attrs = $am[1];
        }
        $css = $m[1];
        $css = preg_replace('/\/\*[\s\S]*?\*\//', '', $css);
        $css = preg_replace('/\s+/', ' ', $css);
        $css = preg_replace('/\s*([:;,{}])\s*/', '$1', $css);
        return '<style' . $attrs . '>' . trim($css) . '</style>';
    }, $content);

    // 2. Minify tag <script> (JS)
    $content = preg_replace_callback('/<script\b[^>]*>([\s\S]*?)<\/script>/i', function($m) {
        $attrs = '';
        if (preg_match('/<script\b([^>]*)>/i', $m[0], $am)) {
            $attrs = $am[1];
        }
        $js = $m[1];
        $js = preg_replace('/\/\*[\s\S]*?\*\//', '', $js);
        $lines = explode("\n", $js);
        $cleanLines = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || strpos($trimmed, '//') === 0) {
                continue;
            }
            $trimmed = preg_replace('/(?<!:)\s*\/\/[^"\']*$/', '', $trimmed);
            if ($trimmed !== '') {
                $cleanLines[] = $trimmed;
            }
        }
        $js = implode(" ", $cleanLines);
        return '<script' . $attrs . '>' . $js . '</script>';
    }, $content);

    // 3. Lindungi elemen sensitif: <pre>, <textarea>, <code>
    $content = preg_replace_callback('/<(pre|textarea|code)\b[^>]*>.*?<\/\1>/is', function ($matches) use (&$placeholders) {
        $key = '___DAP_PROTECTED_BLOCK_' . count($placeholders) . '___';
        $placeholders[$key] = $matches[0];
        return $key;
    }, $content);

    // 4. Minify blok @php ... @endphp
    $content = preg_replace_callback('/@php\b([\s\S]*?)@endphp/i', function ($matches) use (&$placeholders) {
        $phpCode = trim($matches[1]);
        $tokens = @token_get_all('<?php ' . $phpCode);
        $cleanPhp = '';
        foreach ($tokens as $token) {
            if (is_array($token)) {
                if ($token[0] === T_OPEN_TAG) continue;
                if ($token[0] === T_COMMENT || $token[0] === T_DOC_COMMENT) continue;
                if ($token[0] === T_WHITESPACE) {
                    $cleanPhp .= ' ';
                    continue;
                }
                $cleanPhp .= $token[1];
            } else {
                $cleanPhp .= $token;
            }
        }
        $singleLinePhp = preg_replace('/\s+/', ' ', trim($cleanPhp));
        $key = '___DAP_PHP_BLOCK_' . count($placeholders) . '___';
        $placeholders[$key] = '@php ' . $singleLinePhp . ' @endphp';
        return $key;
    }, $content);

    // 5. Hapus komentar Blade & HTML
    $content = preg_replace('/\{\{--[\s\S]*?--\}\}/', '', $content);
    $content = preg_replace('/<!--(?!\s*\[if[\s\S]*?\])[\s\S]*?-->/', '', $content);

    // 6. Padatkan spasi dan tag
    $content = preg_replace('/\s+/', ' ', $content);
    $content = preg_replace('/>\s+</', '><', $content);
    $content = preg_replace('/>\s+(\{\{|\{!!)/', '>$1', $content);
    $content = preg_replace('/(\}\}|\!\})\s+</', '$1<', $content);

    // 7. Kembalikan blok yang dilindungi
    foreach ($placeholders as $key => $originalBlock) {
        $content = str_replace($key, $originalBlock, $content);
    }

    return trim($content);
}
```

### Implementasi Deteksi Heuristik `getFileStatus`
```php
public function getFileStatus(string $filePath): ?array
{
    if (!File::isFile($filePath)) {
        return null;
    }

    $content = File::get($filePath);
    $size = strlen($content);
    $lines = substr_count($content, "\n") + 1;
    $isBlade = \Illuminate\Support\Str::endsWith($filePath, '.blade.php');
    $isJson = \Illuminate\Support\Str::endsWith($filePath, '.json');
    $isEnc = \Illuminate\Support\Str::endsWith($filePath, '.enc');

    if ($isJson || $isEnc) {
        $type = $isEnc ? 'ENC' : 'JSON';
        $isMinified = ($lines <= 1 && $size > 0);
    } elseif ($isBlade) {
        $type = 'Blade';
        $avgLineLen = $size / max(1, $lines);
        $isMinified = ($lines <= 3 && $size > 50) || ($lines <= 15 && $size > 1500 && $avgLineLen > 250);
    } else {
        $type = 'PHP';
        $avgLineLen = $size / max(1, $lines);
        $isMinified = ($lines <= 3 && $size > 100) || ($lines <= 10 && $size > 2000 && $avgLineLen > 250);
    }

    $hasVault = SourceMapVaultService::hasMap($filePath);
    $relPath = str_replace('\\', '/', \Illuminate\Support\Str::after($filePath, base_path() . DIRECTORY_SEPARATOR));

    return [
        'file' => $filePath,
        'rel_path' => $relPath,
        'type' => $type,
        'is_minified' => $isMinified,
        'status' => $isMinified ? 'MINIFIED' : 'ORIGINAL',
        'lines' => $lines,
        'size' => $size,
        'has_vault' => $hasVault,
    ];
}
```

---

## 🧪 4. WORKFLOW PENGUJIAN & VERIFIKASI WAJIB

Setelah seluruh file selesai dibuat, jalankan pengujian siklus penuh (*Full Round-Trip Verification*):

1. **Uji Minify Penuh:**
   ```bash
   php artisan code:minify all --force
   ```
   Pastikan tidak ada error kompilasi dan seluruh file terkompresi.

2. **Cek Status Minifikasi (Harus 100% Minified):**
   ```bash
   php artisan code:status all
   ```
   Verifikasi bahwa baris statistik melaporkan:
   `⚡ Minified Files : XX file (100%)`
   `📄 Original Files : 0 file (0%)`

3. **Uji Pemulihan Penuh (Unminify Round-Trip):**
   ```bash
   php artisan code:unminify all --force
   ```
   Verifikasi bahwa seluruh file berhasil dipulihkan dari SourceMap Vault (100% kode asli).

4. **Cek Status Pemulihan (Harus 100% Original):**
   ```bash
   php artisan code:status all
   ```
   Verifikasi bahwa status melaporkan:
   `⚡ Minified Files : 0 file (0%)`
   `📄 Original Files : XX file (100%)`

5. **Uji Kompilasi Blade & Web Request:**
   ```bash
   php artisan view:clear
   php artisan view:cache
   ```
   Pastikan tidak ada sintaks Blade atau PHP yang rusak akibat proses minifikasi dan unminifikasi.
```
