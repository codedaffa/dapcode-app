<?php

namespace App\Services\Theme;

use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class HolidayThemeService
{
    /**
     * Get the active celebration theme for the current date or session override.
     *
     * @param Carbon|null $date
     * @return array
     */
    public static function getActiveTheme(?Carbon $date = null): array
    {
        // 1. Check for manual override in query string (?theme=...) or session
        if (request()->has('theme')) {
            $requestedTheme = strtolower(request()->query('theme'));
            if ($requestedTheme === 'auto') {
                Session::forget('holiday_theme');
            } else {
                Session::put('holiday_theme', $requestedTheme);
            }
        }

        if (Session::has('holiday_theme') && Session::get('holiday_theme') !== 'auto') {
            $overrideTheme = Session::get('holiday_theme');
            $allThemes = self::getAllThemePresets();
            if (isset($allThemes[$overrideTheme])) {
                $preset = $allThemes[$overrideTheme];
                $preset['is_override'] = true;
                return $preset;
            }
        }

        // 2. Automatically detect based on date (defaults to today)
        $now = $date ?? Carbon::now();
        $month = (int) $now->format('n');
        $day = (int) $now->format('j');
        $year = (int) $now->format('Y');

        $detected = self::detectHolidayByDate($month, $day, $year);
        $detected['is_override'] = false;
        return $detected;
    }

    /**
     * Detect Indonesian holiday by month, day and year.
     *
     * @param int $month
     * @param int $day
     * @param int $year
     * @return array
     */
    protected static function detectHolidayByDate(int $month, int $day, int $year): array
    {
        $hutKe = $year - 1945;

        // 1. HUT Kemerdekaan RI (Agustus: 10 - 20 Agustus)
        if ($month === 8 && $day >= 10 && $day <= 20) {
            return [
                'id' => 'kemerdekaan',
                'css_class' => 'theme-kemerdekaan',
                'name' => "HUT RI ke-{$hutKe} (Kemerdekaan RI)",
                'name_en' => "Indonesian Independence Day ({$hutKe}th Anniversary)",
                'greeting' => "Dirgahayu Republik Indonesia ke-{$hutKe}! Merdeka! 🇮🇩",
                'greeting_en' => "Happy Indonesian Independence Day! 🇮🇩",
                'badge' => "HUT RI ke-{$hutKe}",
                'badge_en' => "Independence Day ({$hutKe}th)",
                'icon' => 'fa-solid fa-flag',
                'primary_color' => '#dc2626',
                'accent_color' => '#ffffff',
                'gradient' => 'linear-gradient(135deg, #dc2626 0%, #ffffff 100%)',
            ];
        }

        // 2. Hari Lahir Pancasila (1 Juni)
        if ($month === 6 && $day >= 1 && $day <= 3) {
            return [
                'id' => 'pancasila',
                'css_class' => 'theme-pancasila',
                'name' => 'Hari Lahir Pancasila',
                'name_en' => 'Pancasila Day',
                'greeting' => 'Selamat Hari Lahir Pancasila 🇮🇩',
                'greeting_en' => 'Happy Pancasila Day 🇮🇩',
                'badge' => 'Hari Lahir Pancasila',
                'badge_en' => 'Pancasila Day',
                'icon' => 'fa-solid fa-shield-halved',
                'primary_color' => '#b91c1c',
                'accent_color' => '#f59e0b',
                'gradient' => 'linear-gradient(135deg, #b91c1c 0%, #f59e0b 100%)',
            ];
        }

        // 3. Hari Sumpah Pemuda (27 - 29 Oktober)
        if ($month === 10 && $day >= 27 && $day <= 29) {
            return [
                'id' => 'pemuda',
                'css_class' => 'theme-pemuda',
                'name' => 'Hari Sumpah Pemuda',
                'name_en' => 'Youth Pledge Day',
                'greeting' => 'Selamat Hari Sumpah Pemuda! Bersatu Bangun Bangsa 🇮🇩',
                'greeting_en' => 'Happy Youth Pledge Day 🇮🇩',
                'badge' => 'Sumpah Pemuda',
                'badge_en' => 'Youth Pledge Day',
                'icon' => 'fa-solid fa-fire-flame-curved',
                'primary_color' => '#ea580c',
                'accent_color' => '#dc2626',
                'gradient' => 'linear-gradient(135deg, #ea580c 0%, #dc2626 100%)',
            ];
        }

        // 4. Hari Pahlawan (9 - 11 November)
        if ($month === 11 && $day >= 9 && $day <= 11) {
            return [
                'id' => 'pahlawan',
                'css_class' => 'theme-pahlawan',
                'name' => 'Hari Pahlawan Nasional',
                'name_en' => 'National Heroes Day',
                'greeting' => 'Selamat Hari Pahlawan! Kobarkan Semangat Juang 🇮🇩',
                'greeting_en' => 'Happy National Heroes Day 🇮🇩',
                'badge' => 'Hari Pahlawan',
                'badge_en' => 'Heroes Day',
                'icon' => 'fa-solid fa-medal',
                'primary_color' => '#c2410c',
                'accent_color' => '#fbbf24',
                'gradient' => 'linear-gradient(135deg, #c2410c 0%, #fbbf24 100%)',
            ];
        }

        // 5. Hari Raya Natal & Libur Akhir Tahun (20 - 31 Desember)
        if ($month === 12 && $day >= 20 && $day <= 31) {
            return [
                'id' => 'natal',
                'css_class' => 'theme-natal',
                'name' => 'Hari Raya Natal & Tahun Baru',
                'name_en' => 'Christmas & Holiday Season',
                'greeting' => 'Selamat Hari Raya Natal & Tahun Baru! Damai Kasih 🎄',
                'greeting_en' => 'Merry Christmas & Happy New Year! 🎄',
                'badge' => 'Natal & Tahun Baru',
                'badge_en' => 'Christmas & New Year',
                'icon' => 'fa-solid fa-tree',
                'primary_color' => '#e11d48',
                'accent_color' => '#15803d',
                'gradient' => 'linear-gradient(135deg, #e11d48 0%, #15803d 100%)',
            ];
        }

        // 6. Tahun Baru Masehi (1 - 3 Januari)
        if ($month === 1 && $day >= 1 && $day <= 3) {
            return [
                'id' => 'tahunbaru',
                'css_class' => 'theme-tahunbaru',
                'name' => "Tahun Baru {$year}",
                'name_en' => "New Year {$year}",
                'greeting' => "Selamat Tahun Baru {$year}! Semoga Penuh Sukses & Berkah ✨",
                'greeting_en' => "Happy New Year {$year}! Wishing You Success ✨",
                'badge' => "Tahun Baru {$year}",
                'badge_en' => "New Year {$year}",
                'icon' => 'fa-solid fa-champagne-glasses',
                'primary_color' => '#a855f7',
                'accent_color' => '#38bdf8',
                'gradient' => 'linear-gradient(135deg, #a855f7 0%, #38bdf8 100%)',
            ];
        }

        // 7. Tahun Baru Imlek (Januari - Februari)
        if (($month === 1 && $day >= 25) || ($month === 2 && $day <= 18)) {
            return [
                'id' => 'imlek',
                'css_class' => 'theme-imlek',
                'name' => 'Tahun Baru Imlek (Gong Xi Fa Cai)',
                'name_en' => 'Chinese New Year (Lunar Year)',
                'greeting' => 'Selamat Tahun Baru Imlek! Gong Xi Fa Cai 🧧🏮',
                'greeting_en' => 'Happy Chinese New Year! Gong Xi Fa Cai 🧧🏮',
                'badge' => 'Tahun Baru Imlek',
                'badge_en' => 'Lunar New Year',
                'icon' => 'fa-solid fa-dragon',
                'primary_color' => '#dc2626',
                'accent_color' => '#fbbf24',
                'gradient' => 'linear-gradient(135deg, #dc2626 0%, #fbbf24 100%)',
            ];
        }

        // 8. Ramadhan & Idul Fitri (Maret - April)
        if (($month === 3 && $day >= 15) || ($month === 4 && $day <= 15)) {
            return [
                'id' => 'idulfitri',
                'css_class' => 'theme-idulfitri',
                'name' => 'Hari Raya Idul Fitri (Lebaran)',
                'name_en' => 'Eid Mubarak (Idul Fitri)',
                'greeting' => 'Selamat Hari Raya Idul Fitri! Minal Aidin Wal Faizin, Mohon Maaf Lahir & Batin 🌙✨',
                'greeting_en' => 'Happy Eid Mubarak! 🌙✨',
                'badge' => 'Idul Fitri',
                'badge_en' => 'Eid Mubarak',
                'icon' => 'fa-solid fa-moon',
                'primary_color' => '#059669',
                'accent_color' => '#fbbf24',
                'gradient' => 'linear-gradient(135deg, #059669 0%, #fbbf24 100%)',
            ];
        }

        // 9. Hari Kartini (20 - 22 April)
        if ($month === 4 && $day >= 20 && $day <= 22) {
            return [
                'id' => 'kartini',
                'css_class' => 'theme-kartini',
                'name' => 'Hari Kartini',
                'name_en' => 'Kartini Day',
                'greeting' => 'Selamat Hari Kartini! Habis Gelap Terbitlah Terang 🌸',
                'greeting_en' => 'Happy Kartini Day 🌸',
                'badge' => 'Hari Kartini',
                'badge_en' => 'Kartini Day',
                'icon' => 'fa-solid fa-seedling',
                'primary_color' => '#ec4899',
                'accent_color' => '#8b5cf6',
                'gradient' => 'linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%)',
            ];
        }

        // 10. Hari Raya Waisak (Mei - Awal Juni)
        if ($month === 5 && $day >= 20 && $day <= 31) {
            return [
                'id' => 'waisak',
                'css_class' => 'theme-waisak',
                'name' => 'Hari Raya Waisak',
                'name_en' => 'Vesak Day',
                'greeting' => 'Selamat Hari Raya Waisak! Damai dan Kebahagiaan bagi Semua 🪷',
                'greeting_en' => 'Happy Vesak Day! Peace and Harmony 🪷',
                'badge' => 'Hari Raya Waisak',
                'badge_en' => 'Vesak Day',
                'icon' => 'fa-solid fa-dharmachakra',
                'primary_color' => '#d97706',
                'accent_color' => '#f59e0b',
                'gradient' => 'linear-gradient(135deg, #d97706 0%, #f59e0b 100%)',
            ];
        }

        // 11. Default Theme (Hari Biasa / Standar DapCode)
        return self::getDefaultTheme();
    }

    /**
     * Get default standard theme.
     *
     * @return array
     */
    public static function getDefaultTheme(): array
    {
        return [
            'id' => 'default',
            'css_class' => 'theme-default',
            'name' => 'Tema Standar DapCode',
            'name_en' => 'DapCode Cyber Indigo (Default)',
            'greeting' => 'Selamat Datang di Sistem HMVC DapCode',
            'greeting_en' => 'Welcome to DapCode HMVC Ecosystem',
            'badge' => 'DapCode Standard',
            'badge_en' => 'DapCode Standard',
            'icon' => 'fa-solid fa-cube',
            'primary_color' => '#6366f1',
            'accent_color' => '#38bdf8',
            'gradient' => 'linear-gradient(135deg, #6366f1 0%, #38bdf8 100%)',
        ];
    }

    /**
     * Get all holiday theme presets for manual preview & selector.
     *
     * @return array
     */
    public static function getAllThemePresets(): array
    {
        $year = (int) date('Y');
        $hutKe = $year - 1945;

        return [
            'default' => self::getDefaultTheme(),
            'kemerdekaan' => [
                'id' => 'kemerdekaan',
                'css_class' => 'theme-kemerdekaan',
                'name' => "HUT RI ke-{$hutKe} (17 Agustus)",
                'name_en' => "Indonesian Independence Day ({$hutKe}th)",
                'greeting' => "Dirgahayu Republik Indonesia ke-{$hutKe}! Merdeka! 🇮🇩",
                'greeting_en' => "Happy Indonesian Independence Day! 🇮🇩",
                'badge' => "HUT RI ke-{$hutKe}",
                'badge_en' => "Independence Day ({$hutKe}th)",
                'icon' => 'fa-solid fa-flag',
                'primary_color' => '#dc2626',
                'accent_color' => '#ffffff',
                'gradient' => 'linear-gradient(135deg, #dc2626 0%, #ffffff 100%)',
            ],
            'idulfitri' => [
                'id' => 'idulfitri',
                'css_class' => 'theme-idulfitri',
                'name' => 'Hari Raya Idul Fitri (Lebaran)',
                'name_en' => 'Eid Mubarak (Idul Fitri)',
                'greeting' => 'Selamat Hari Raya Idul Fitri! Minal Aidin Wal Faizin 🌙✨',
                'greeting_en' => 'Happy Eid Mubarak! 🌙✨',
                'badge' => 'Idul Fitri',
                'badge_en' => 'Eid Mubarak',
                'icon' => 'fa-solid fa-moon',
                'primary_color' => '#059669',
                'accent_color' => '#fbbf24',
                'gradient' => 'linear-gradient(135deg, #059669 0%, #fbbf24 100%)',
            ],
            'imlek' => [
                'id' => 'imlek',
                'css_class' => 'theme-imlek',
                'name' => 'Tahun Baru Imlek (Gong Xi Fa Cai)',
                'name_en' => 'Chinese New Year (Gong Xi Fa Cai)',
                'greeting' => 'Selamat Tahun Baru Imlek! Gong Xi Fa Cai 🧧🏮',
                'greeting_en' => 'Happy Chinese New Year! Gong Xi Fa Cai 🧧🏮',
                'badge' => 'Tahun Baru Imlek',
                'badge_en' => 'Lunar New Year',
                'icon' => 'fa-solid fa-dragon',
                'primary_color' => '#dc2626',
                'accent_color' => '#fbbf24',
                'gradient' => 'linear-gradient(135deg, #dc2626 0%, #fbbf24 100%)',
            ],
            'natal' => [
                'id' => 'natal',
                'css_class' => 'theme-natal',
                'name' => 'Hari Raya Natal & Tahun Baru',
                'name_en' => 'Christmas & New Year Season',
                'greeting' => 'Selamat Hari Raya Natal & Tahun Baru! Damai Kasih 🎄',
                'greeting_en' => 'Merry Christmas & Happy New Year! 🎄',
                'badge' => 'Natal & Tahun Baru',
                'badge_en' => 'Christmas & New Year',
                'icon' => 'fa-solid fa-tree',
                'primary_color' => '#e11d48',
                'accent_color' => '#15803d',
                'gradient' => 'linear-gradient(135deg, #e11d48 0%, #15803d 100%)',
            ],
            'pancasila' => [
                'id' => 'pancasila',
                'css_class' => 'theme-pancasila',
                'name' => 'Hari Lahir Pancasila (1 Juni)',
                'name_en' => 'Pancasila Day (1 June)',
                'greeting' => 'Selamat Hari Lahir Pancasila 🇮🇩',
                'greeting_en' => 'Happy Pancasila Day 🇮🇩',
                'badge' => 'Hari Lahir Pancasila',
                'badge_en' => 'Pancasila Day',
                'icon' => 'fa-solid fa-shield-halved',
                'primary_color' => '#b91c1c',
                'accent_color' => '#f59e0b',
                'gradient' => 'linear-gradient(135deg, #b91c1c 0%, #f59e0b 100%)',
            ],
            'pemuda' => [
                'id' => 'pemuda',
                'css_class' => 'theme-pemuda',
                'name' => 'Hari Sumpah Pemuda (28 Oktober)',
                'name_en' => 'Youth Pledge Day (28 Oct)',
                'greeting' => 'Selamat Hari Sumpah Pemuda! Bersatu Bangun Bangsa 🇮🇩',
                'greeting_en' => 'Happy Youth Pledge Day 🇮🇩',
                'badge' => 'Sumpah Pemuda',
                'badge_en' => 'Youth Pledge Day',
                'icon' => 'fa-solid fa-fire-flame-curved',
                'primary_color' => '#ea580c',
                'accent_color' => '#dc2626',
                'gradient' => 'linear-gradient(135deg, #ea580c 0%, #dc2626 100%)',
            ],
            'waisak' => [
                'id' => 'waisak',
                'css_class' => 'theme-waisak',
                'name' => 'Hari Raya Waisak',
                'name_en' => 'Vesak Day',
                'greeting' => 'Selamat Hari Raya Waisak 🪷',
                'greeting_en' => 'Happy Vesak Day 🪷',
                'badge' => 'Hari Raya Waisak',
                'badge_en' => 'Vesak Day',
                'icon' => 'fa-solid fa-dharmachakra',
                'primary_color' => '#d97706',
                'accent_color' => '#f59e0b',
                'gradient' => 'linear-gradient(135deg, #d97706 0%, #f59e0b 100%)',
            ],
            'kartini' => [
                'id' => 'kartini',
                'css_class' => 'theme-kartini',
                'name' => 'Hari Kartini (21 April)',
                'name_en' => 'Kartini Day (21 April)',
                'greeting' => 'Selamat Hari Kartini! Habis Gelap Terbitlah Terang 🌸',
                'greeting_en' => 'Happy Kartini Day 🌸',
                'badge' => 'Hari Kartini',
                'badge_en' => 'Kartini Day',
                'icon' => 'fa-solid fa-seedling',
                'primary_color' => '#ec4899',
                'accent_color' => '#8b5cf6',
                'gradient' => 'linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%)',
            ],
        ];
    }
}
