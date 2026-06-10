<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TrainingBatchController;
use App\Http\Controllers\WidyaiswaraController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\ConflictController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicController;

// Landing / login page
Route::get('/', function () {
    if (Auth::check()) return redirect('/admin/dashboard');
    return view('welcome');
})->name('login');

// Public routes (no login)
Route::get('/catalog',  [PublicController::class, 'catalog']);
Route::get('/calendar', [PublicController::class, 'calendar']);

// Auth
Route::post('/login',  [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// ── Admin (protected) ─────────────────────────────────────────
Route::middleware(['auth', 'ensure.active'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/',          fn() => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Read-only views accessible to all authenticated users
    Route::get('/catalog',  [CatalogController::class, 'index'])->name('catalog');
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar');

    // ── Templates ─────────────────────────────────────────────
    Route::middleware('permission:manage_templates')->group(function () {
        Route::get('/templates',                              [TemplateController::class, 'index'])->name('templates.index');
        Route::get('/templates/create',                       [TemplateController::class, 'create'])->name('templates.create');
        Route::get('/templates/import',                       [TemplateController::class, 'importForm'])->name('templates.import');
        Route::post('/templates/import',                      [TemplateController::class, 'import'])->name('templates.import.store');
        Route::get('/templates/download-csv',                 [TemplateController::class, 'downloadCsvTemplate'])->name('templates.download-csv');
        Route::post('/templates',                             [TemplateController::class, 'store'])->name('templates.store');
        Route::get('/templates/{template}',                   [TemplateController::class, 'show'])->name('templates.show');
        Route::get('/templates/{template}/edit',              [TemplateController::class, 'edit'])->name('templates.edit');
        Route::put('/templates/{template}',                   [TemplateController::class, 'update'])->name('templates.update');
        Route::delete('/templates/{template}',                [TemplateController::class, 'destroy'])->name('templates.destroy');
        Route::post('/templates/{template}/phases',           [TemplateController::class, 'storePhase'])->name('templates.phases.store');
        Route::put('/templates/{template}/phases/{phase}',    [TemplateController::class, 'updatePhase'])->name('templates.phases.update');
        Route::delete('/templates/{template}/phases/{phase}', [TemplateController::class, 'destroyPhase'])->name('templates.phases.destroy');
    });

    // ── Training Batches ──────────────────────────────────────
    Route::middleware('permission:manage_batches')->group(function () {
        Route::get('/batches',                   [TrainingBatchController::class, 'index'])->name('batches.index');
        Route::get('/batches/create',            [TrainingBatchController::class, 'create'])->name('batches.create');
        Route::post('/batches',                  [TrainingBatchController::class, 'store'])->name('batches.store');
        Route::get('/batches/{batch}',           [TrainingBatchController::class, 'show'])->name('batches.show');
        Route::delete('/batches/{batch}',        [TrainingBatchController::class, 'destroy'])->name('batches.destroy');
        Route::post('/batches/{batch}/generate', [TrainingBatchController::class, 'generate'])->name('batches.generate');
        Route::post('/batches/{batch}/schedule', [TrainingBatchController::class, 'updateSchedule'])->name('batches.schedule.update');
    });

    // ── Widyaiswaras & WI Assignment ──────────────────────────
    Route::middleware('permission:assign_wi')->group(function () {
        Route::get('/widyaiswaras',                   [WidyaiswaraController::class, 'index'])->name('widyaiswaras.index');
        Route::post('/widyaiswaras',                  [WidyaiswaraController::class, 'store'])->name('widyaiswaras.store');
        Route::put('/widyaiswaras/{widyaiswara}',     [WidyaiswaraController::class, 'update'])->name('widyaiswaras.update');
        Route::delete('/widyaiswaras/{widyaiswara}',  [WidyaiswaraController::class, 'destroy'])->name('widyaiswaras.destroy');
        Route::post('/schedules/{schedule}/assign-wi', [WidyaiswaraController::class, 'assignWi'])->name('schedules.assign-wi');
        Route::delete('/wi-assignments/{assignment}',  [WidyaiswaraController::class, 'removeAssignment'])->name('wi-assignments.destroy');
    });

    // ── Holidays ──────────────────────────────────────────────
    Route::middleware('permission:manage_holidays')->group(function () {
        Route::get('/holidays',                          [HolidayController::class, 'index'])->name('holidays.index');
        Route::post('/holidays',                         [HolidayController::class, 'store'])->name('holidays.store');
        Route::get('/holidays/download-csv',             [HolidayController::class, 'downloadCsvTemplate'])->name('holidays.download-csv');
        Route::post('/holidays/import',                  [HolidayController::class, 'import'])->name('holidays.import');
        Route::post('/holidays/bulk',                    [HolidayController::class, 'bulkStore'])->name('holidays.bulk');
        Route::post('/holidays/revert-conflict',         [HolidayController::class, 'revertConflict'])->name('holidays.revert-conflict');
        Route::put('/holidays/{holiday}',                [HolidayController::class, 'update'])->name('holidays.update');
        Route::delete('/holidays/{holiday}',             [HolidayController::class, 'destroy'])->name('holidays.destroy');
    });

    Route::middleware('permission:manage_batches')->group(function () {
        Route::post('/batches/{batch}/dismiss-conflict', [HolidayController::class, 'dismissConflictAlert'])->name('batches.dismiss-conflict');
    });

    // ── Conflicts ─────────────────────────────────────────────
    Route::middleware('permission:monitor_conflicts')->group(function () {
        Route::get('/conflicts', [ConflictController::class, 'index'])->name('conflicts.index');
    });

    // ── User Management (super_admin only) ────────────────────
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/users',           [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users',          [AdminUserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}',    [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});
