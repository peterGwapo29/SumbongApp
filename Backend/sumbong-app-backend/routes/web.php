<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RequestManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ServiceTypeManagementController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }
        if ($user->isAdmin()) {
            return redirect('/admin');
        }
        // For authenticated non-admin users, show a welcome message or redirect to a safe page
        return view('welcome');
    }
    return redirect()->route('login');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Request Management with full CRUD
    Route::resource('requests', RequestManagementController::class);
    Route::post('requests/{id}/status', [RequestManagementController::class, 'updateStatus'])->name('requests.status');
    Route::post('requests/{id}/assign', [RequestManagementController::class, 'assign'])->name('requests.assign');
    
    Route::resource('users', UserManagementController::class)->only(['index', 'show']);
    Route::resource('service-types', ServiceTypeManagementController::class);
});

// Profile routes (accessible to all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
