<?php

use App\Http\Controllers\DevPreviewController;
use App\Livewire\BillOfLadingForm;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/select-company', [App\Http\Controllers\CompanySelectionController::class, 'index'])->name('company.select');
    Route::post('/select-company', [App\Http\Controllers\CompanySelectionController::class, 'select'])->name('company.select.store');
});

// --- Plataforma BL ---
Route::middleware(['auth', 'active', 'company'])->group(function () {
    Route::get('dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
        ->middleware('verified')
        ->name('dashboard');

    Route::get('/bl', [App\Http\Controllers\BillOfLadingController::class, 'index'])->name('bl.index');
    Route::get('/bl/create/{billOfLading?}', [App\Http\Controllers\BillOfLadingCreateController::class, 'show'])->name('bl.create');
    Route::get('/bl/{billOfLading}', [App\Http\Controllers\BillOfLadingController::class, 'show'])->name('bl.show');
    Route::get('/bl/{billOfLading}/pdf', [App\Http\Controllers\BillOfLadingController::class, 'pdf'])->name('bl.pdf');
    Route::post('/bl/{billOfLading}/void-request', [App\Http\Controllers\BillOfLadingController::class, 'requestVoid'])->name('bl.void-request');
    Route::post('/void-requests/{voidRequest}/approve', [App\Http\Controllers\BillOfLadingController::class, 'approveVoid'])->name('bl.void-request.approve');
    Route::post('/void-requests/{voidRequest}/reject', [App\Http\Controllers\BillOfLadingController::class, 'rejectVoid'])->name('bl.void-request.reject');

    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

    Route::middleware('can:users.manage')->group(function () {
        Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [App\Http\Controllers\UserController::class, 'create'])->name('users.create');
        Route::post('/users', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle-active', [App\Http\Controllers\UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::delete('/users/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
    });
});

// Vista previa del formato para pruebas visuales -- protegida: solo admin/gerencia, y con login.
Route::middleware(['auth', 'can:users.manage'])->group(function () {
    Route::get('/dev/bl-preview/{portCode}', [DevPreviewController::class, 'blPreview'])
        ->name('dev.bl-preview');
});

// Logout propio: esta versión de Breeze (Volt) no registra una ruta "logout" tradicional.
Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->middleware('auth')->name('logout');

require __DIR__.'/auth.php';
