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
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Mail;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');

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
Route::get('/get-last-package-id', [PackageController::class, 'getLastPackageID']);
Route::post('/upload', [FileUploadController::class, 'upload'])->name('upload');
Route::post('/saveAndNotify',[DashboardController::class,'savePackage'])->name('saveAndNotify');
Route::post('/update-csv', [FileUploadController::class, 'updateCsv'])->name('update.csv');

// messages
Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
Route::post('/send-message', [MessageController::class, 'sendMessage'])->name('messages.send');
Route::post('/send-reply', [MessageController::class, 'sendReply'])->name('send.reply');
Route::post('/textblast', [MessageController::class, 'sendTextBlast'])->name('messages.textblast');

// PackageLogs
Route::get('/packagelogs',[PackageController::class, 'index'])->middleware(['auth'])->name('packagelogs');
Route::get('/get-packages',[PackageController::class, 'getPackages'])->middleware(['auth'])->name('packages');
Route::get('/get-packages-by-mailbox/{mailbox}',[PackageController::class, 'getPackagesByMailbox'])->name('packages.by.mailbox');
Route::post('/check-tracking',[PackageController::class, 'checkTrackingNumberExist'])->name('check.tracking.number');
Route::post('/outgoing-packge',[PackageController::class,'outgoingPackage'])->middleware(['auth'])->name('outgoing.package');
Route::post('/delete-package', [PackageController::class, 'deletePackage'])->name('delete.package');
Route::post('/updatePackageStatus', [PackageController::class, 'updateStatus'])->name('package.updateStatus');

// Storage label printing routes
Route::get('/labels', [LabelController::class, 'index'])->middleware(['auth'])->name('labels.index');
Route::get('/labels/single/{id}', [LabelController::class, 'generateSingle'])->middleware(['auth'])->name('labels.single');

// Company management routes (requires authentication)
Route::middleware(['auth'])->group(function () {
    // Admin routes (super admin only)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::get('/health-check', [AdminController::class, 'healthCheck'])->name('health-check');
    });

    // Company routes
    Route::resource('companies', CompanyController::class);
    Route::post('/companies/{company}/switch', [CompanyController::class, 'switchCompany'])->name('companies.switch');

    // User management routes
    Route::resource('users', UserController::class);
    Route::put('/users/{user}/assign-role', [UserController::class, 'assignRole'])->name('users.assign-role');
});


Route::get('/test-email', function () {
    Mail::raw('This is a test email from Laravel via Gmail SMTP.', function ($message) {
        $message->to('khairo.smile@gmail.com')
                ->subject('Test Email');
    });
    return 'Email sent!';
});
