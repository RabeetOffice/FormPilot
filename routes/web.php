<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\RoutingRuleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WhiteLabelController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\FormSourceController;
use App\Http\Controllers\Api\FormSubmissionController;
use App\Http\Middleware\CheckWorkspaceRole;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Public API routes (no auth required)
Route::prefix('api')->group(function () {
    Route::post('/f/{apiKey}', [FormSubmissionController::class, 'submit'])->name('api.form.submit');
    Route::post('/webhook/{apiKey}', [FormSubmissionController::class, 'webhook'])->name('api.form.webhook');
    Route::get('/test/{apiKey}', [FormSubmissionController::class, 'test'])->name('api.form.test');
});

// Workspace creation (before workspace middleware)
Route::middleware('auth')->group(function () {
    Route::get('/workspaces/create', [WorkspaceController::class, 'create'])->name('workspaces.create');
    Route::post('/workspaces', [WorkspaceController::class, 'store'])->name('workspaces.store');
    Route::post('/workspaces/{workspace}/switch', [WorkspaceController::class, 'switchWorkspace'])->name('workspaces.switch');
});

// Authenticated + workspace-scoped routes
Route::middleware(['auth', 'verified', \App\Http\Middleware\EnsureWorkspaceAccess::class])->group(function () {

    // ─────────────────────────────────────────────────────────
    // ALL ROLES (viewer+) — Read-only access
    // ─────────────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (own profile)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Submissions — read-only for viewers
    Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');

    // Brands — read-only for viewers
    Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('/brands/{brand}', [BrandController::class, 'show'])->name('brands.show');

    // Domains — read-only for viewers
    Route::get('/domains', [DomainController::class, 'index'])->name('domains.index');

    // Form sources — read-only for viewers
    Route::get('/form-sources', [FormSourceController::class, 'index'])->name('formsources.index');

    // Routing rules — read-only for viewers
    Route::get('/routing-rules', [RoutingRuleController::class, 'index'])->name('routing-rules.index');

    // Notifications — all roles can see their own
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');

    // Setup instructions — read-only for viewers
    Route::get('/setup', [SetupController::class, 'index'])->name('setup.index');

    // ─────────────────────────────────────────────────────────
    // SALES REP+ — Work with submissions
    // ─────────────────────────────────────────────────────────
    Route::middleware(CheckWorkspaceRole::class . ':sales_rep')->group(function () {
        Route::patch('/submissions/{submission}/status', [SubmissionController::class, 'updateStatus'])->name('submissions.updateStatus');
        Route::patch('/submissions/{submission}/notes', [SubmissionController::class, 'updateNotes'])->name('submissions.updateNotes');
        Route::post('/submissions/{submission}/reassign', [SubmissionController::class, 'reassign'])->name('submissions.reassign');
    });

    // ─────────────────────────────────────────────────────────
    // ADMIN+ — Full management of brands, domains, team, routing, form sources
    // ─────────────────────────────────────────────────────────
    Route::middleware(CheckWorkspaceRole::class . ':admin')->group(function () {
        // Brands — full CRUD
        Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
        Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
        Route::get('/brands/{brand}/edit', [BrandController::class, 'edit'])->name('brands.edit');
        Route::put('/brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
        Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');

        // Domains — create, delete, regenerate key
        Route::get('/domains/create', [DomainController::class, 'create'])->name('domains.create');
        Route::post('/domains', [DomainController::class, 'store'])->name('domains.store');
        Route::delete('/domains/{domain}', [DomainController::class, 'destroy'])->name('domains.destroy');
        Route::post('/domains/{domain}/regenerate-key', [DomainController::class, 'regenerateKey'])->name('domains.regenerateKey');

        // Routing rules — create, update, delete
        Route::post('/routing-rules', [RoutingRuleController::class, 'store'])->name('routing-rules.store');
        Route::put('/routing-rules/{routing_rule}', [RoutingRuleController::class, 'update'])->name('routing-rules.update');
        Route::delete('/routing-rules/{routing_rule}', [RoutingRuleController::class, 'destroy'])->name('routing-rules.destroy');

        // Form sources — create, delete
        Route::post('/form-sources', [FormSourceController::class, 'store'])->name('formsources.store');
        Route::delete('/form-sources/{formSource}', [FormSourceController::class, 'destroy'])->name('formsources.destroy');

        // Team — view, invite, update roles, remove
        Route::get('/team', [TeamController::class, 'index'])->name('team.index');
        Route::post('/team/invite', [TeamController::class, 'invite'])->name('team.invite');
        Route::patch('/team/{user}/role', [TeamController::class, 'updateRole'])->name('team.updateRole');
        Route::delete('/team/{user}', [TeamController::class, 'remove'])->name('team.remove');

        // White-label settings
        Route::get('/white-label', [WhiteLabelController::class, 'index'])->name('whitelabel.index');
        Route::post('/white-label', [WhiteLabelController::class, 'update'])->name('whitelabel.update');
    });

    // ─────────────────────────────────────────────────────────
    // OWNER ONLY — Billing, workspace-level settings
    // ─────────────────────────────────────────────────────────
    Route::middleware(CheckWorkspaceRole::class . ':owner')->group(function () {
        Route::get('/billing', function () {
            return view('billing.index');
        })->name('billing.index');
    });
});

require __DIR__.'/auth.php';
