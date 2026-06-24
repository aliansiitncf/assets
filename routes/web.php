<?php

use App\Http\Controllers\AssetLabelPrint;
use App\Http\Controllers\SsoClientController;
use App\Livewire\Asset\AssetCreate;
use App\Livewire\Asset\AssetEdit;
use App\Livewire\Asset\AssetPage;
use App\Livewire\Asset\AssetReport;
use App\Livewire\Asset\PageAssetDamage;
use App\Livewire\Asset\PageAssetRepair;
use App\Livewire\Audit\AuditLog;
use App\Livewire\Auth\Login;
use App\Livewire\Category\PageCategory;
use App\Livewire\Component\PageComponent;
use App\Livewire\Dashboard;
use App\Livewire\Location\LocationPage;
use App\Livewire\Permission\PermissionPage;
use App\Livewire\Role\RolePage;
use App\Livewire\Setting\PageSetup;
use App\Livewire\Users\Index;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware('guest')->group(function () {
    Route::get('/', Login::class)->name('login');
    Route::get('/sso/callback', [SsoClientController::class, 'callback'])->name('sso.callback');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    // User Management
    Route::get('/users', Index::class)->name('users');
    Route::get('/permissions', PermissionPage::class)->name('permissions');
    Route::get('/roles', RolePage::class)->name('roles');

    // Category Management
    Route::get('/categories', PageCategory::class)->name('categories');

    // Location Management
    Route::get('/locations', LocationPage::class)->name('locations');

    // Component Management
    Route::get('/components', PageComponent::class)->name('components');

    // Assets Management
    Route::get('/assets', AssetPage::class)->name('assets');
    Route::get('/assets/create', AssetCreate::class)->name('asset.create');
    Route::get('/assets/{asset}/edit', AssetEdit::class)->name('asset.edit');
    Route::get('/assets-damage', PageAssetDamage::class)->name('asset.damage');
    Route::get('/assets/repair', PageAssetRepair::class)->name('asset.repair');
    Route::get('/assets-report', AssetReport::class)->name('asset-report');

    // PAGE SETUP
    Route::get('page-setup', PageSetup::class)->name('page-setup');
    Route::get('/asset/print-label', [AssetLabelPrint::class, 'print'])
        ->name('asset.print.label');

    // AUDIT LOGS
    Route::get('audit-logs', AuditLog::class)->name('audit-logs');
    // Route::get('audit-logs/export', [AssetExport::class, 'exportAuditLogsPdf'])->name('auditLogs.pdf');

});
