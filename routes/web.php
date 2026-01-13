<?php
// Web routes for the Borrower Trust Score app.

use App\Http\Controllers\BorrowerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/borrowers', [BorrowerController::class, 'index'])->name('borrowers.index');
Route::post('/borrowers', [BorrowerController::class, 'store'])->name('borrowers.store');
Route::put('/borrowers/{borrower}', [BorrowerController::class, 'update'])->name('borrowers.update');
Route::delete('/borrowers/{borrower}', [BorrowerController::class, 'destroy'])->name('borrowers.destroy');
Route::get('/borrowers/{borrower}', [BorrowerController::class, 'show'])->name('borrowers.show');

Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::post('/items', [ItemController::class, 'store'])->name('items.store');
Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
Route::post('/loans/{loan}/return', [LoanController::class, 'processReturn'])->name('loans.return');
