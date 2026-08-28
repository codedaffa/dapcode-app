<?php

namespace App\Services\Vite;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class ViteHelper
{
    /**
     * Generate HTML tags for the given entry points (dev server or production build).
     *
     * @param string|array $entrypoints
     * @param string $buildDirectory
     * @return HtmlString
     */
    public static function tags($entrypoints, string $buildDirectory = 'build'): HtmlString
    {
        $entrypoints = is_array($entrypoints) ? $entrypoints : [$entrypoints];
        $hotFile = public_path('hot');

        // 1. Vite Dev Server Running (hot file exists)
        if (file_exists($hotFile)) {
            $url = rtrim(trim(file_get_contents($hotFile)), '/');
            $tags = [];
            $tags[] = sprintf('<script type="module" src="%s/@vite/client"></script>', $url);

            foreach ($entrypoints as $entry) {
                if (Str::endsWith($entry, ['.css', '.scss', '.sass', '.less'])) {
                    $tags[] = sprintf('<link rel="stylesheet" href="%s/%s">', $url, ltrim($entry, '/'));
                } else {
                    $tags[] = sprintf('<script type="module" src="%s/%s"></script>', $url, ltrim($entry, '/'));
                }
            }

            return new HtmlString(implode("\n    ", $tags));
        }

        // 2. Production Manifest
        $manifestPath = public_path($buildDirectory . '/manifest.json');
        if (!file_exists($manifestPath)) {
            $manifestPath = public_path($buildDirectory . '/.vite/manifest.json');
        }

        if (!file_exists($manifestPath)) {
            return new HtmlString('');
        }

        $manifest = json_decode(file_get_contents($manifestPath), true) ?: [];
        $tags = [];

        foreach ($entrypoints as $entry) {
            $normalizedKey = ltrim($entry, '/');
            if (!isset($manifest[$normalizedKey])) {
                continue;
            }

            $chunk = $manifest[$normalizedKey];
            $file = $chunk['file'];

            // CSS imported by this chunk
            if (isset($chunk['css']) && is_array($chunk['css'])) {
                foreach ($chunk['css'] as $cssFile) {
                    $tags[] = sprintf('<link rel="stylesheet" href="%s/%s">', asset($buildDirectory), $cssFile);
                }
            }

            if (Str::endsWith($file, ['.css', '.scss', '.sass', '.less'])) {
                $tags[] = sprintf('<link rel="stylesheet" href="%s/%s">', asset($buildDirectory), $file);
            } else {
                $tags[] = sprintf('<script type="module" src="%s/%s"></script>', asset($buildDirectory), $file);
            }
        }

        return new HtmlString(implode("\n    ", $tags));
    }
}
