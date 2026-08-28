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

Route::get('/', function () {
    return view('portfolio', [
        'title' => __('common.portfolio_home'),
        'pageTitle' => __('common.app_name') . ' - ' . __('common.portfolio_home'),
    ]);
});

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
