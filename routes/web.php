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
use App\Http\Controllers\AnnualCalendarController;
use App\Http\Controllers\ConflictGroupController;

// Landing / login page
Route::get('/', function () {
    if (Auth::check()) return redirect('/admin/dashboard');
    return view('welcome');
})->name('login');

// Public routes (no login)
Route::get('/catalog',  [PublicController::class, 'catalog'])->name('public.catalog');
Route::get('/calendar', [PublicController::class, 'calendar'])->name('public.calendar');

// Auth
Route::post('/login',  [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

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
        Route::put('/templates/{template}',                   [TemplateController::class, 'update'])->name('templates.update');
        Route::delete('/templates/{template}',                [TemplateController::class, 'destroy'])->name('templates.destroy');
        Route::post('/templates/{template}/phases',           [TemplateController::class, 'storePhase'])->name('templates.phases.store');
        Route::post('/templates/{template}/import-phases',    [TemplateController::class, 'importPhases'])->name('templates.phases.import');
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
        Route::post('/batches/{batch}/link-activity',   [TrainingBatchController::class, 'linkActivity'])->name('batches.link-activity');
        Route::delete('/batches/{batch}/link-activity', [TrainingBatchController::class, 'unlinkActivity'])->name('batches.unlink-activity');
        Route::post('/batches/{batch}/override',        [TrainingBatchController::class, 'setOverride'])->name('batches.override.set');
        Route::delete('/batches/{batch}/override',       [TrainingBatchController::class, 'clearOverride'])->name('batches.override.clear');
    });

    // ── Annual Calendar (Kalender Tahunan) — optional planning source for batches ──
    Route::middleware('permission:manage_annual_calendar')->group(function () {
        Route::get('/annual-calendars',                                [AnnualCalendarController::class, 'index'])->name('annual-calendars.index');
        Route::get('/annual-calendars/create',                         [AnnualCalendarController::class, 'create'])->name('annual-calendars.create');
        Route::post('/annual-calendars',                               [AnnualCalendarController::class, 'store'])->name('annual-calendars.store');
        Route::get('/annual-calendars/{calendar}',                     [AnnualCalendarController::class, 'show'])->name('annual-calendars.show');
        Route::delete('/annual-calendars/{calendar}',                  [AnnualCalendarController::class, 'destroy'])->name('annual-calendars.destroy');
        Route::post('/annual-calendars/{calendar}/archive',            [AnnualCalendarController::class, 'archive'])->name('annual-calendars.archive');
        Route::post('/annual-calendars/{calendar}/unarchive',          [AnnualCalendarController::class, 'unarchive'])->name('annual-calendars.unarchive');
        Route::post('/annual-calendars/{calendar}/activities',         [AnnualCalendarController::class, 'storeActivity'])->name('annual-calendars.activities.store');
        Route::put('/annual-calendars/{calendar}/activities/{activity}',    [AnnualCalendarController::class, 'updateActivity'])->name('annual-calendars.activities.update');
        Route::delete('/annual-calendars/{calendar}/activities/{activity}', [AnnualCalendarController::class, 'destroyActivity'])->name('annual-calendars.activities.destroy');
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
        Route::get('/conflicts',                      [ConflictController::class, 'index'])->name('conflicts.index');
        Route::post('/conflict-alerts/{alert}/resolve', [ConflictController::class, 'resolveAlert'])->name('conflict-alerts.resolve');
        Route::post('/conflict-alerts/{alert}/ignore',  [ConflictController::class, 'ignoreAlert'])->name('conflict-alerts.ignore');
    });

    // ── Conflict Groups / Rules (canonical sequence mapping config) ──
    Route::middleware('permission:manage_conflict_groups')->group(function () {
        Route::get('/conflict-groups',                       [ConflictGroupController::class, 'index'])->name('conflict-groups.index');
        Route::post('/conflict-groups',                      [ConflictGroupController::class, 'store'])->name('conflict-groups.store');
        Route::put('/conflict-groups/{group}',                [ConflictGroupController::class, 'update'])->name('conflict-groups.update');
        Route::delete('/conflict-groups/{group}',              [ConflictGroupController::class, 'destroy'])->name('conflict-groups.destroy');
        Route::post('/conflict-groups/{group}/toggle-active', [ConflictGroupController::class, 'toggleActive'])->name('conflict-groups.toggle-active');
        Route::put('/conflict-rules/{rule}',                   [ConflictGroupController::class, 'updateRule'])->name('conflict-rules.update');
        Route::delete('/conflict-rules/{rule}',                [ConflictGroupController::class, 'destroyRule'])->name('conflict-rules.destroy');
        Route::post('/conflict-rules/{rule}/toggle',          [ConflictGroupController::class, 'toggleRule'])->name('conflict-rules.toggle');
    });

    // ── User Management (super_admin only) ────────────────────
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/users',           [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users',          [AdminUserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}',    [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });
});
