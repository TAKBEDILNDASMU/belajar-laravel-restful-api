<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/users', [UserController::class, 'register'])->name('register');
Route::post('/users/login', [UserController::class, 'login'])->name('login');
Route::middleware(ApiAuthMiddleware::class)->group(function () {

    // User API
    Route::get('/users/current', [UserController::class, 'get'])->name('get.current');
    Route::patch('/users/current', [UserController::class, 'update'])->name('update.current');
    Route::delete('/users/logout', [UserController::class, 'logout'])->name('logout');

    // Contact API
    Route::post('/contacts', [ContactController::class, 'create'])->name('contact.create');
    Route::get('/contacts/{contactId}', [ContactController::class, 'get'])->name('contact.get');
    Route::put('/contacts/{contactId}', [ContactController::class, 'update'])->name('contact.update');
    Route::delete('/contacts/{contactId}', [ContactController::class, 'delete'])->name('contact.delete');
});
