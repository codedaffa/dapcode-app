<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('vite', function ($expression) {
            return "<?php echo \\App\\Services\\Vite\\ViteHelper::tags($expression); ?>";
        });

        // Security Execution Boundary for Application Views
        \Illuminate\Support\Facades\View::composer('portfolio', function () {
            \App\Services\Dapcode\LicenseGuard::assertModuleAllowed('portfolio');
        });
    }
}
