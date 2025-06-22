<?php

use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\RegisterController;
use Illuminate\Support\Facades\Route;

/* Login */
Route::get('/login', [LoginController::class, 'logout']);

/* Register */
Route::get('/register', function () {
    return view('register-user');
});

Route::post('/register/user', [RegisterController::class, 'create']);

Route::middleware('auth_user')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
});

