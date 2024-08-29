<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use App\Http\Middleware\ValidateAddressId;
use App\Http\Middleware\ValidateContactId;
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
    Route::get('/contacts', [ContactController::class, 'search'])->name('contact.search');
    Route::get('/contacts/{contactId}', [ContactController::class, 'get'])->name('contact.get')->middleware(ValidateContactId::class);
    Route::put('/contacts/{contactId}', [ContactController::class, 'update'])->name('contact.update')->middleware(ValidateContactId::class);
    Route::delete('/contacts/{contactId}', [ContactController::class, 'delete'])->name('contact.delete')->middleware(ValidateContactId::class);

    // Address API
    Route::post('/contacts/{contactId}/addresses', [AddressController::class, 'create'])->name('address.create')->middleware(ValidateContactId::class);
    Route::get('/contacts/{contactId}/addresses', [AddressController::class, 'list'])->name('address.list')->middleware(ValidateContactId::class);
    Route::put('/contacts/{contactId}/addresses/{addressId}', [AddressController::class, 'update'])->name('address.update')->middleware([ValidateContactId::class, ValidateAddressId::class]);
    Route::get('/contacts/{contactId}/addresses/{addressId}', [AddressController::class, 'get'])->name('address.get')->middleware([ValidateContactId::class, ValidateAddressId::class]);
    Route::delete('/contacts/{contactId}/addresses/{addressId}', [AddressController::class, 'delete'])->name('address.delete')->middleware([ValidateContactId::class, ValidateAddressId::class]);
});
