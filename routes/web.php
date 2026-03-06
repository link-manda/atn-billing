<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/scheduler', function () {
    Artisan::call('schedule:run');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    Route::get('/clients', fn () => view('clients'))->name('clients.index');
    Route::get('/clinics', fn () => view('clinics'))->name('clinics.index');
    Route::get('/products', fn () => view('products'))->name('products.index');

    Route::get('/subscriptions', fn () => view('subscriptions'))->name('subscriptions.index');
    Route::get('/invoices', fn () => view('invoices'))->name('invoices.index');
    Route::get('/invoices/{id}', fn ($id) => view('invoice-detail', compact('id')))->name('invoices.show');
    Route::get('/payments', fn () => view('payments'))->name('payments.index');
    Route::get('/licenses', fn () => view('licenses'))->name('licenses.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
