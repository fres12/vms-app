<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Livewire\VisitorForm;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\VisitorFormController;
use App\Http\Controllers\BarcodeController;

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// Route login
Route::get('/login', [VisitorController::class, 'showLogin'])->name('login');
Route::post('/login', [VisitorController::class, 'loginAdmin'])->name('login.submit');
Route::post('/logout', [VisitorController::class, 'logout'])->name('logout');

// Route publik untuk form visitor
Route::get('/', [VisitorFormController::class, 'create'])->name('visitor.form');
Route::post('/', [VisitorController::class, 'store'])->name('visitor.store');

// Contoh routes untuk barcode
Route::get('/barcode', [BarcodeController::class, 'show'])->name('barcode.show');
Route::get('/barcode-save', [BarcodeController::class, 'save'])->name('barcode.save');
Route::get('/barcode-blade', [BarcodeController::class, 'blade'])->name('barcode.blade');

// Route yang butuh login admin
Route::middleware(['admin.auth'])->group(function () {
    Route::get('/visitor-list', [VisitorController::class, 'index'])->name('visitor.list');
    Route::get('/visitors/export', [VisitorController::class, 'export'])->name('visitors.export');
    Route::get('/visitors/{id}/approve', [VisitorController::class, 'approve'])->name('visitors.approve');
    Route::get('/visitors/{id}/reject', [VisitorController::class, 'reject'])->name('visitors.reject');
    Route::post('/visitors/{id}/status', [VisitorController::class, 'updateStatus'])->name('visitors.updateStatus');
    Route::post('/change-password', [VisitorController::class, 'changePassword'])->name('change.password');
    Route::post('/admin/change-password', [VisitorController::class, 'changePassword'])->name('admin.change-password');
    Route::get('/barcode/{ticket_number}', [VisitorController::class, 'viewBarcode'])->name('barcode.view');
});
