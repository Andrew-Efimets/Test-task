<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Chats\ChatController;
use App\Http\Controllers\Chats\MessageController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');


Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login.form');
Route::post('/login', [AuthController::class, 'login'])
    ->name('login');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])
    ->name('register.form');
Route::post('/register', [AuthController::class, 'register'])
    ->name('register');
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])
    ->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])
    ->name('password.update');


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/email/verify', function () {
        return view('verify-email');
    })->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/')->with('success', 'Email verify successfully!');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'New link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');

    Route::prefix('chats')->name('chats.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])
            ->name('index');
        Route::get('/{chat}', [ChatController::class, 'show'])
            ->name('show');
        Route::post('/private', [ChatController::class, 'storePrivate'])
            ->name('private.store');
        Route::post('/group', [ChatController::class, 'storeGroup'])
            ->name('group.store');
        Route::delete('/{chat}', [ChatController::class, 'destroyChat'])
            ->name('destroy');
        Route::post('/{chat}/participants', [ChatController::class, 'addParticipant'])
            ->name('participants.add');
        Route::post('/{chat}/messages', [MessageController::class, 'store'])
            ->name('messages.store');
        Route::put('/messages/{message}', [MessageController::class, 'update'])
            ->name('messages.update');
        Route::delete('/messages/{message}', [MessageController::class, 'destroy'])
            ->name('messages.destroy');
    });
});
