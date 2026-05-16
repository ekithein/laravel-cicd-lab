<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/creativity/{id}', [PageController::class, 'category'])->name('creativity.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/cabinet', [CabinetController::class, 'index'])->name('cabinet');
    Route::get('/cabinet/master-classes/create', [CabinetController::class, 'create'])->name('master-classes.create');
    Route::post('/cabinet/master-classes', [CabinetController::class, 'store'])->name('master-classes.store');
    Route::get('/cabinet/master-classes/{id}/edit', [CabinetController::class, 'edit'])->name('master-classes.edit');
    Route::post('/cabinet/master-classes/{id}', [CabinetController::class, 'update'])->name('master-classes.update');
    Route::get('/master-classes/{id}/confirm', [EnrollmentController::class, 'confirm'])->name('enrollments.confirm');
    Route::post('/master-classes/{id}/enroll', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::post('/master-classes/{id}/cancel', [EnrollmentController::class, 'cancel'])->name('enrollments.cancel');
});
