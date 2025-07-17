<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Livewire\VisitorForm;
use App\Http\Controllers\VisitorController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
    Route::get('/visitors/{id}/approve', [VisitorController::class, 'approve'])->name('visitors.approve');
    Route::get('/visitors/{id}/reject', [VisitorController::class, 'reject'])->name('visitors.reject');
    Route::get('/visitor-list', [VisitorController::class, 'index'])->name('visitor.list');
    Route::get('/visitors/export', [VisitorController::class, 'export'])->name('visitors.export');
    Route::post('/visitors/{id}/status', [VisitorController::class, 'updateStatus'])->name('visitors.updateStatus');
});

// Route login TIDAK dalam group middleware
Route::get('/login', [VisitorController::class, 'showLogin'])->name('login');
Route::post('/login', [VisitorController::class, 'loginAdmin'])->name('login.submit');
Route::post('/logout', [VisitorController::class, 'logout'])->name('logout');

// Route publik
Route::view('/visitor-form', 'visitor-form')->name('visitor.form');
Route::post('/visitors', [VisitorController::class, 'store'])->name('visitors.store');
Route::get('/visitors', [VisitorController::class, 'index'])->name('visitors.index');

// Route yang butuh login admin
Route::middleware(['admin.auth'])->group(function () {
    Route::get('/visitor-list', [VisitorController::class, 'index'])->name('visitor.list');
    Route::get('/visitors/export', [VisitorController::class, 'export'])->name('visitors.export');
    Route::get('/visitors/{id}/approve', [VisitorController::class, 'approve'])->name('visitors.approve');
    Route::get('/visitors/{id}/reject', [VisitorController::class, 'reject'])->name('visitors.reject');
    Route::post('/visitors/{id}/status', [VisitorController::class, 'updateStatus'])->name('visitors.updateStatus');
});
