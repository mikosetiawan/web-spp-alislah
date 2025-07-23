<?php

use App\Http\Controllers\ClassController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SppCostController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Student Routes
    Route::resource('students', StudentController::class)->withTrashed(['show']);
    Route::post('students/{student}/restore', [StudentController::class, 'restore'])->name('students.restore');
    Route::delete('students/{student}/force-delete', [StudentController::class, 'forceDelete'])->name('students.force-delete');
    Route::get('/students/{student}/unpaid-months', [PaymentController::class, 'getUnpaidMonths'])
        ->name('students.unpaid-months');

    // Payment Routes
    Route::prefix('payments')->group(function () {
        // Daftar pembayaran
        Route::get('/', [PaymentController::class, 'index'])->name('payments.index');
        
        // Laporan pembayaran
        Route::get('/report', [PaymentController::class, 'report'])->name('payments.report');
        Route::get('/report/print', [PaymentController::class, 'printReport'])->name('payments.print-report');
        Route::get('/report/export', [PaymentController::class, 'exportReport'])->name('payments.export-report');

        // Pembayaran per siswa
        Route::prefix('students/{student}')->group(function () {
            Route::get('/', [PaymentController::class, 'show'])->name('students.payments.show');
            Route::get('/create', [PaymentController::class, 'create'])->name('students.payments.create');
            Route::post('/', [PaymentController::class, 'store'])->name('students.payments.store');
        });
        
        // Cetak kwitansi
        Route::get('/receipt/{payment}/print', [PaymentController::class, 'printReceipt'])
            ->name('payments.receipt.print');
    });

    // Class Routes
    Route::resource('classes', ClassController::class);
    
    // SPP Costs Routes (Biaya SPP per Kelas)
    Route::prefix('spp-costs')->group(function () {
        Route::get('/', [SppCostController::class, 'index'])->name('spp-costs.index');
        Route::get('/create', [SppCostController::class, 'create'])->name('spp-costs.create');
        Route::post('/', [SppCostController::class, 'store'])->name('spp-costs.store');
        Route::get('/{sppCost}/edit', [SppCostController::class, 'edit'])->name('spp-costs.edit');
        Route::put('/{sppCost}', [SppCostController::class, 'update'])->name('spp-costs.update');
        Route::delete('/{sppCost}', [SppCostController::class, 'destroy'])->name('spp-costs.destroy');
        
        // Get SPP cost by class and year
        Route::get('/get-by-class', [SppCostController::class, 'getByClass'])->name('spp-costs.get-by-class');
    });
});

require __DIR__ . '/auth.php';