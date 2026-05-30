<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TimerController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('tasks')->name('tasks.')->group(function () {
    Route::post('/', [TaskController::class, 'store'])->name('store');
    Route::put('/{task}', [TaskController::class, 'update'])->name('update');
    Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
});

Route::post('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');

Route::prefix('timer')->name('timer.')->group(function () {
    Route::post('/complete', [TimerController::class, 'complete'])->name('complete');
    Route::get('/today', [TimerController::class, 'today'])->name('today');
});
