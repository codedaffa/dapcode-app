<?php

namespace App\Providers;

use App\Http\Controllers\HMVCController;
use App\Services\HMVC\HMVC;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class HMVCServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Bind HMVC singleton
        $this->app->singleton(HMVC::class, function () {
            return new HMVC();
        });

        // Bind Template library singleton
        $this->app->singleton(\App\Libraries\Template::class, function () {
            return new \App\Libraries\Template();
        });

        $this->app->alias(\App\Libraries\Template::class, 'template');

        // Load helpers
        $helperPath = app_path('Helpers/hmvc.php');
        if (file_exists($helperPath)) {
            require_once $helperPath;
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $modulesPath = app_path('Modules');

        if (File::isDirectory($modulesPath)) {
            $modules = File::directories($modulesPath);

            foreach ($modules as $moduleDir) {
                $moduleName = basename($moduleDir);
                $viewKey = strtolower($moduleName);
                $viewsPath = $moduleDir . DIRECTORY_SEPARATOR . 'Views';
                $migrationsPath = $moduleDir . DIRECTORY_SEPARATOR . 'Database' . DIRECTORY_SEPARATOR . 'Migrations';

                // Auto-register View namespace (e.g. view('home::index') or view('Home::index'))
                if (File::isDirectory($viewsPath)) {
                    View::addNamespace($moduleName, $viewsPath);
                    View::addNamespace(strtolower($moduleName), $viewsPath);
                    View::addNamespace(Str::kebab($moduleName), $viewsPath);
                    View::addNamespace(Str::snake($moduleName), $viewsPath);

                    // Mandatory Security View Composer for Protected Module View Namespace (Layer 4)
                    View::composer([
                        "{$moduleName}::*",
                        "{$viewKey}::*",
                    ], function () use ($viewKey) {
                        \App\Services\Dapcode\LicenseGuard::assertModuleAllowed($viewKey);
                    });
                }

                // Auto-register Migrations (if present)
                if (File::isDirectory($migrationsPath)) {
                    $this->loadMigrationsFrom($migrationsPath);
                }
            }
        }

        // Register Dynamic HMVC Routes (Runs automatically without manual route definitions)
        $this->registerDynamicRoutes();
    }

    /**
     * Register dynamic routes for HMVC modules without manual route declarations.
     *
     * @return void
     */
    protected function registerDynamicRoutes()
    {
        Route::middleware(['web', 'dapcode.license'])
            ->group(function () {
                Route::any(
                    '{module}/{segment2?}/{segment3?}/{params?}',
                    [HMVCController::class, 'handle']
                )->where([
                    // Exclude storage, sanctum, api, lang switcher, theme switcher, dapcode license endpoints, etc.
                    'module' => '^(?!api$|_debugbar$|sanctum$|lang$|theme$|dapcode$)[A-Za-z0-9\-_]+$',
                    'params' => '.*',
                ]);
            });
    }
}
