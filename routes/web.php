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

// Agent self-assign ("Tomar Ticket")
Route::patch('tickets/{ticket}/take', [TicketController::class, 'take'])
    ->middleware('auth')
    ->name('tickets.take');

Route::resource('tickets.comments', CommentController::class)
    ->only(['store', 'destroy'])
    ->middleware('auth');

require __DIR__ . '/auth.php';


Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/users', [AdminController::class, 'users'])
        ->name('users');

    Route::get('/users/create', [AdminController::class, 'createUser'])
        ->name('users.create');

    Route::post('/users', [AdminController::class, 'storeUser'])
        ->name('users.store');

    Route::patch('/users/{user}/role', [AdminController::class, 'updateRole'])
        ->name('users.updateRole');

    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])
        ->name('users.destroy');

    Route::patch('/users/{user}/restore', [AdminController::class, 'restoreUser'])
        ->name('users.restore');

    Route::get('/stats', [AdminController::class, 'stats'])
        ->name('stats');

     Route::post('/stats/export', [AdminController::class, 'export'])
        ->name('stats.export');

    Route::get('/trash', [AdminController::class, 'trash'])
        ->name('trash');

    Route::patch('/trash/{id}/restore', [AdminController::class, 'restore'])
        ->name('trash.restore');

    Route::delete('/trash/{id}', [AdminController::class, 'forceDelete'])
        ->name('trash.force-delete');
});