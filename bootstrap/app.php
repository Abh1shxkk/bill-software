<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/superadmin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => App\Http\Middleware\AdminMiddleware::class,
            'user' => App\Http\Middleware\UserMiddleware::class,
            'permission' => App\Http\Middleware\CheckPermission::class,
            'module.access' => App\Http\Middleware\CheckModuleAccess::class,
            'super_admin' => App\Http\Middleware\SuperAdminMiddleware::class,
            'license' => App\Http\Middleware\CheckLicense::class,
            'tenant' => App\Http\Middleware\TenantMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
