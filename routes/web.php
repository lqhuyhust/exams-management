<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\SubmissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// authentication routes
Route::get('/login', [LoginController::class, 'index'])->name('login.index');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // exams routes
    Route::get('/', [ExamController::class, 'index'])->name('exams');
    Route::get('/exam/{examID}', [ExamController::class, 'show'])->name('exams.show');
    Route::post('/exam/{examID}/register', [ExamController::class, 'register'])->name('exams.register');
    Route::post('/exam/{examID}/submit', [ExamController::class, 'submit'])->name('exams.submit');
    Route::post('/exam/{examID}/store-submission', [ExamController::class, 'storeSubmissionToRedis'])->name('exams.store-submission');

});