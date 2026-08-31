<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [\App\Http\Controllers\PortfolioController::class, 'index'])->name('home');

// Language Switcher Route
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

// Manual Theme Switcher Route
Route::get('/theme/{themeKey}', function ($themeKey) {
    $validPresets = array_keys(\App\Services\Theme\HolidayThemeService::getAllThemePresets());
    $validPresets[] = 'auto';

    if (in_array($themeKey, $validPresets)) {
        if ($themeKey === 'auto') {
            session()->forget('holiday_theme');
        } else {
            session(['holiday_theme' => $themeKey]);
        }
    }

    return redirect()->back();
})->name('theme.switch');

// DapCode License & Activation Routes
Route::prefix('dapcode')->group(function () {
    Route::get('/activate', [\App\Http\Controllers\Dapcode\LicenseController::class, 'showActivate'])->name('dapcode.activate');
    Route::post('/activate', [\App\Http\Controllers\Dapcode\LicenseController::class, 'activate'])->name('dapcode.activate.post');
    Route::post('/deactivate', [\App\Http\Controllers\Dapcode\LicenseController::class, 'deactivate'])->name('dapcode.deactivate');
    Route::get('/status', [\App\Http\Controllers\Dapcode\LicenseController::class, 'status'])->name('dapcode.status');
    Route::get('/terminal', [\App\Http\Controllers\Dapcode\LicenseController::class, 'showTerminal'])->name('dapcode.terminal');
    Route::post('/terminal/sign', [\App\Http\Controllers\Dapcode\LicenseController::class, 'signPayload'])->name('dapcode.terminal.sign');
    Route::post('/terminal/artisan', [\App\Http\Controllers\Dapcode\LicenseController::class, 'executeArtisan'])->name('dapcode.terminal.artisan');
});

