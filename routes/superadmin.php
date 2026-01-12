<?php

use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\OrganizationController;
use App\Http\Controllers\SuperAdmin\LicenseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "super_admin" middleware group.
|
*/

Route::prefix('superadmin')->name('superadmin.')->middleware(['auth', 'super_admin'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', fn() => redirect()->route('superadmin.dashboard'));

    // Organizations
    Route::resource('organizations', OrganizationController::class);
    Route::post('organizations/{organization}/suspend', [OrganizationController::class, 'suspend'])->name('organizations.suspend');
    Route::post('organizations/{organization}/activate', [OrganizationController::class, 'activate'])->name('organizations.activate');

    // Licenses
    Route::resource('licenses', LicenseController::class)->except(['edit', 'update', 'destroy']);
    Route::post('licenses/{license}/extend', [LicenseController::class, 'extend'])->name('licenses.extend');
    Route::post('licenses/{license}/suspend', [LicenseController::class, 'suspend'])->name('licenses.suspend');
    Route::post('licenses/{license}/reactivate', [LicenseController::class, 'reactivate'])->name('licenses.reactivate');
    Route::post('licenses/{license}/renew', [LicenseController::class, 'renew'])->name('licenses.renew');
    Route::post('licenses/{license}/regenerate', [LicenseController::class, 'regenerate'])->name('licenses.regenerate');
    Route::post('licenses/validate', [LicenseController::class, 'validate'])->name('licenses.validate');
});
