<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
});

// Protected routes with role-based access
Route::middleware(['auth', 'role'])->group(function () {
    // Admin routes - full access to destinations, schedules, and users CRUD
    Route::middleware('role:admin')->group(function () {
        Route::resource('destinations', DestinationController::class);
        Route::resource('schedules', ScheduleController::class);
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });
    
    // Kasir routes - read access to destinations and schedules, full access to transactions
    Route::middleware('role:kasir,admin')->group(function () {
        Route::get('destinations', [DestinationController::class, 'index'])->name('destinations.index');
        Route::get('destinations/{destination}', [DestinationController::class, 'show'])->name('destinations.show');
        Route::get('destinations/export/csv', [DestinationController::class, 'export'])->name('destinations.export');
        Route::get('schedules', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('schedules/{schedule}', [ScheduleController::class, 'show'])->name('schedules.show');
        Route::get('schedules/export/csv', [ScheduleController::class, 'export'])->name('schedules.export');
        
        Route::resource('transactions', TransactionController::class);
        Route::patch('transactions/{transaction}/confirm-payment', [TransactionController::class, 'confirmPayment'])->name('transactions.confirm-payment');
        Route::get('transactions/{transaction}/print', [TransactionController::class, 'printTickets'])->name('transactions.print');
    });
    
    // Schedules - read access for kasir, boarding, admin
    Route::middleware('role:kasir,boarding,admin')->group(function () {
        Route::get('schedules', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('schedules/{schedule}', [ScheduleController::class, 'show'])->name('schedules.show');
    });
    
    // Boarding specific routes - validation access
    Route::middleware('role:boarding,admin')->group(function () {
        
        // Ticket validation routes
        Route::get('tickets/validate', [TicketController::class, 'validateForm'])->name('tickets.validate.form');
        Route::post('tickets/validate', [TicketController::class, 'processValidation'])->name('tickets.validate');
        Route::post('tickets/search', [TicketController::class, 'searchByCode'])->name('tickets.search');
    });
});
