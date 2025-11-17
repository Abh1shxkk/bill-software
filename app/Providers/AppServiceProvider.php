<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use App\Models\StockLedger;
use App\Models\Batch;
use App\Observers\StockLedgerObserver;
use App\Observers\BatchObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force correct base URL when not running in console
        if (! $this->app->runningInConsole()) {
            // Detect if running in a subdirectory
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $baseUrl = '';
            
            // Extract base path from script name
            if ($scriptName && $scriptName !== '/index.php') {
                $baseUrl = str_replace('/index.php', '', dirname($scriptName));
                $baseUrl = str_replace('\\', '/', $baseUrl);
                $baseUrl = rtrim($baseUrl, '/');
            }
            
            // Build full URL
            $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $rootUrl = $scheme . '://' . $host . $baseUrl;
            
            URL::forceRootUrl($rootUrl);
            URL::forceScheme($scheme);
        }

        // Register observers for automatic data sync
        StockLedger::observe(StockLedgerObserver::class);
        Batch::observe(BatchObserver::class);
    }
}
