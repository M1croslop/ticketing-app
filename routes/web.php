<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('tickets', TicketController::class)
    ->middleware('auth');


Route::resource('tickets.comments', CommentController::class)
    ->only(['store', 'destroy'])
    ->middleware('auth');

require __DIR__ . '/auth.php';


Route::middleware(['auth'])->group(function () {

    Route::get('/admin/users', [AdminController::class, 'users'])
        ->name('admin.users');

    Route::put('/admin/users/{user}/role', [AdminController::class, 'updateRole'])
        ->name('admin.users.role');

    Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])
        ->name('admin.users.destroy');

    Route::post('/admin/users/{id}/restore', [AdminController::class, 'restoreUser'])
        ->name('admin.users.restore');

    Route::get('/admin/stats', [AdminController::class, 'stats'])
        ->name('admin.stats');

});