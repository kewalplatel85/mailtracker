<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PackageController;

Route::get('/', function () {
    return view('auth.login');
});

// auth users
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// dashboard
Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
Route::post('/upload', [FileUploadController::class, 'upload'])->name('upload');
Route::post('/saveAndNotify',[DashboardController::class,'savePackage'])->name('saveAndNotify');
// messages
Route::get('/sms/inbox', [MessageController::class, 'showMessages'])->name('inbox');
Route::post('/send-reply', [MessageController::class, 'sendReply'])->name('send.reply');
// PackageLogs
Route::get('/packagelogs',[PackageController::class, 'index'])->middleware(['auth'])->name('packagelogs');
Route::get('/get-packages',[PackageController::class, 'getPackages'])->middleware(['auth'])->name('packages');
Route::post('/check-tracking',[PackageController::class, 'checkTrackingNumberExist'])->middleware(['auth'])->name('check.tracking.number');
Route::post('/outgoing-packge',[PackageController::class,'outgoingPackage'])->middleware(['auth'])->name('outgoing.package');
