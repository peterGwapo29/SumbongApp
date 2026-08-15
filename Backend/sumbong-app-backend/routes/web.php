<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RequestManagementController;
use App\Http\Controllers\Admin\ServiceTypeManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if (! $user->relationLoaded('role')) {
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

Route::get('/auth/api-bridge', function (Request $request) {
    $token = $request->query('token');

    if (! $token) {
        return redirect()->route('login')->with('error', 'Missing login token.');
    }

    $accessToken = PersonalAccessToken::findToken($token);

    if (! $accessToken) {
        return redirect()->route('login')->with('error', 'Invalid or expired login token.');
    }

    $user = $accessToken->tokenable;
    $user->load('role');

    if (! $user->isAdmin()) {
        return redirect()->route('login')->with('error', 'Admin access required.');
    }

    Auth::guard('web')->login($user);
    $request->session()->regenerate();

    return redirect()->route('admin.dashboard');
})->middleware('throttle:10,1');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Request Management (users create requests; admins can view, edit, update, and delete)
    Route::resource('requests', RequestManagementController::class)->only([
        'index',
        'show',
        'edit',
        'update',
        'destroy',
    ]);
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
